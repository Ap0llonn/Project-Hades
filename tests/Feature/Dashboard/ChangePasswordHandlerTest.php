<?php

namespace Tests\Feature\Dashboard;

use App\Features\Dashboard\Settings\ChangePassword\ChangePasswordCommand;
use App\Features\Dashboard\Settings\ChangePassword\ChangePasswordHandler;
use App\Models\KeyWrapper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rotates_password_wrapper_when_password_changes(): void
    {
        $currentPassword = 'CurrentMasterPassword123!';
        $newPassword = 'BrandNewMasterPassword456!';
        $initialWrapper = $this->fakeWrappedDekPayload('initial');
        $rotatedWrapper = $this->fakeWrappedDekPayload('rotated');
        $user = $this->createUserWithVault($currentPassword, $initialWrapper);

        $handler = app(ChangePasswordHandler::class);
        $handler->handle(new ChangePasswordCommand(
            userId: (string) $user->id,
            currentPassword: $currentPassword,
            newPassword: $newPassword,
            wrappedDek: $rotatedWrapper,
        ));

        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password_hash));
        $this->assertFalse(Hash::check($currentPassword, $user->password_hash));

        $activeWrapper = KeyWrapper::query()
            ->where('vault_id', $user->vault->id)
            ->where('type', 'password')
            ->whereNull('revoked_at')
            ->latest('id')
            ->first();

        $this->assertNotNull($activeWrapper);

        $allPasswordWrappers = KeyWrapper::query()
            ->where('vault_id', $user->vault->id)
            ->where('type', 'password')
            ->get();

        $this->assertCount(2, $allPasswordWrappers);
        $this->assertSame(1, $allPasswordWrappers->whereNotNull('revoked_at')->count());

        $this->assertSame($rotatedWrapper['ciphertext'], $activeWrapper->ciphertext);
        $this->assertSame($rotatedWrapper['iv'], $activeWrapper->nonce);
        $this->assertSame($rotatedWrapper['salt'], $activeWrapper->prf_salt);
        $this->assertSame('argon2id13', data_get($activeWrapper->prf_params, 'algorithm'));
        $this->assertSame(3, data_get($activeWrapper->prf_params, 'opsLimit'));
        $this->assertSame(65536, data_get($activeWrapper->prf_params, 'memoryKb'));
        $this->assertSame('Argon2id13', data_get($activeWrapper->prf_params, 'type'));
        $this->assertSame(256, data_get($activeWrapper->prf_params, 'keyLengthBits'));
    }

    /**
     * @param array{
     *   ciphertext: string,
     *   iv: string,
     *   salt: string,
     *   keyLengthBits: int,
     *   kdf: array{
     *     algorithm: string,
     *     opsLimit: int,
     *     memoryKb: int,
     *     type: string
     *   }
     * } $wrapper
     */
    private function createUserWithVault(string $password, array $wrapper): User
    {
        $email = 'change.password.' . uniqid('', true) . '@example.com';
        $normalizedEmail = strtolower(trim($email));

        $user = User::query()->create([
            'email' => $email,
            'email_hashed' => hash_hmac('sha256', $normalizedEmail, (string) config('app.key')),
            'password_hash' => Hash::make($password),
            'first_name' => 'Change',
            'last_name' => 'Password',
            'private_key_wrapper' => [
                'version' => 2,
                'wrapped_private_key' => [
                    'ciphertext' => base64_encode(random_bytes(64)),
                    'iv' => base64_encode(random_bytes(24)),
                ],
            ],
            'public_key' => base64_encode(random_bytes(32)),
            'kdf_salt' => $wrapper['salt'],
            'kdf_params' => [
                'algorithm' => $wrapper['kdf']['algorithm'],
                'opsLimit' => $wrapper['kdf']['opsLimit'],
                'memoryKb' => $wrapper['kdf']['memoryKb'],
                'type' => $wrapper['kdf']['type'],
                'keyLengthBits' => $wrapper['keyLengthBits'],
            ],
            'email_verified' => true,
        ]);

        $vault = $user->vault()->create();

        KeyWrapper::query()->create([
            'vault_id' => $vault->id,
            'type' => 'password',
            'ciphertext' => $wrapper['ciphertext'],
            'nonce' => $wrapper['iv'],
            'tag' => null,
            'prf_salt' => $wrapper['salt'],
            'prf_params' => [
                'algorithm' => $wrapper['kdf']['algorithm'],
                'opsLimit' => $wrapper['kdf']['opsLimit'],
                'memoryKb' => $wrapper['kdf']['memoryKb'],
                'type' => $wrapper['kdf']['type'],
                'keyLengthBits' => $wrapper['keyLengthBits'],
            ],
            'credential_id' => null,
            'passkey_uuid' => null,
            'metadata' => ['purpose' => 'dek_wrapper'],
            'revoked_at' => null,
        ]);

        return $user->fresh(['vault']);
    }

    private function fakeWrappedDekPayload(string $seed): array
    {
        return [
            'ciphertext' => base64_encode("ciphertext:{$seed}"),
            'iv' => base64_encode("nonce:{$seed}"),
            'salt' => base64_encode("salt:{$seed}"),
            'keyLengthBits' => 256,
            'kdf' => [
                'algorithm' => 'argon2id13',
                'opsLimit' => 3,
                'memoryKb' => 65536,
                'type' => 'Argon2id13',
            ],
        ];
    }
}
