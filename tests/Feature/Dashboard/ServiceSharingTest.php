<?php

namespace Tests\Feature\Dashboard;

use App\Http\Middleware\EnsureVaultDomain;
use App\Models\ServiceShare;
use App\Models\User;
use App\Models\VaultService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ServiceSharingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureVaultDomain::class);
    }

    public function test_authenticated_user_can_lookup_recipient_public_key(): void
    {
        $owner = $this->createUser('owner@example.com', 'owner-public-key');
        $recipient = $this->createUser('recipient@example.com', 'recipient-public-key');

        $this->actingAs($owner)
            ->getJson(route('service.share.recipient', ['email' => 'recipient@example.com']))
            ->assertOk()
            ->assertJsonPath('data.user_id', (string) $recipient->id)
            ->assertJsonPath('data.public_key', 'recipient-public-key');
    }

    public function test_owner_can_share_service_with_recipient_and_store_asymmetric_envelope(): void
    {
        $owner = $this->createUser('owner@example.com', 'owner-public-key');
        $recipient = $this->createUser('recipient@example.com', 'recipient-public-key');
        $service = $this->createService($owner);

        $this->actingAs($owner)
            ->postJson(route('service.share.create', ['serviceId' => $service->id]), [
                'recipient_email' => 'recipient@example.com',
                'key_envelope' => [
                    'ciphertextBase64' => 'encrypted-share-key',
                    'algorithm' => 'libsodium.crypto_box_seal',
                    'version' => 1,
                ],
            ])
            ->assertCreated()
            ->assertJsonPath('data.service_id', (string) $service->id)
            ->assertJsonPath('data.recipient_user_id', (string) $recipient->id)
            ->assertJsonPath('data.key_envelope.algorithm', 'libsodium.crypto_box_seal');

        $this->assertDatabaseHas('service_shares', [
            'service_id' => (string) $service->id,
            'owner_user_id' => (string) $owner->id,
            'recipient_user_id' => (string) $recipient->id,
        ]);
    }

    public function test_recipient_can_list_incoming_shared_services(): void
    {
        $owner = $this->createUser('owner@example.com', 'owner-public-key');
        $recipient = $this->createUser('recipient@example.com', 'recipient-public-key');
        $service = $this->createService($owner);

        ServiceShare::query()->create([
            'service_id' => (string) $service->id,
            'owner_user_id' => (string) $owner->id,
            'recipient_user_id' => (string) $recipient->id,
            'key_envelope' => [
                'ciphertextBase64' => 'sealed-box',
                'algorithm' => 'libsodium.crypto_box_seal',
                'version' => 1,
            ],
        ]);

        $this->actingAs($recipient)
            ->getJson(route('service.share.incoming'))
            ->assertOk()
            ->assertJsonPath('meta.count', 1)
            ->assertJsonPath('data.0.owner_user_id', (string) $owner->id)
            ->assertJsonPath('data.0.service_id', (string) $service->id)
            ->assertJsonPath('data.0.key_envelope.algorithm', 'libsodium.crypto_box_seal')
            ->assertJsonPath('data.0.service.payload.ciphertextBase64', 'encrypted-payload');
    }

    private function createUser(string $email, string $publicKey): User
    {
        $normalizedEmail = strtolower(trim($email));

        return User::query()->create([
            'email' => $normalizedEmail,
            'email_hashed' => hash_hmac('sha256', $normalizedEmail, (string) config('app.key')),
            'password_hash' => Hash::make('MasterPassword123!'),
            'first_name' => 'Test',
            'last_name' => 'User',
            'private_key_wrapper' => [
                'version' => 2,
                'wrapped_private_key' => [
                    'ciphertext' => 'private-key-ciphertext',
                    'iv' => 'private-key-iv',
                ],
            ],
            'public_key' => $publicKey,
            'kdf_salt' => 'kdf-salt',
            'kdf_params' => [
                'algorithm' => 'argon2id13',
                'opsLimit' => 3,
                'memoryKb' => 65536,
                'type' => 'Argon2id13',
                'keyLengthBits' => 256,
            ],
            'email_verified' => true,
        ]);
    }

    private function createService(User $owner): VaultService
    {
        return VaultService::query()->create([
            'user_id' => (string) $owner->id,
            'type' => 'login',
            'name' => 'Encrypted Item',
            'favorite' => false,
            'status' => 'active',
            'data' => [
                'ciphertextBase64' => 'encrypted-payload',
                'ivBase64' => 'payload-iv',
                'algorithm' => 'libsodium.crypto_secretbox',
                'schema' => 1,
            ],
        ]);
    }
}
