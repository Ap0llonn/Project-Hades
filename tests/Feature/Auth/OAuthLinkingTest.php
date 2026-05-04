<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureVaultDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OAuthLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureVaultDomain::class);
    }

    public function test_oauth_linking_routes_require_authentication(): void
    {
        $this
            ->get(route('settings.security.oauth.link', ['provider' => 'google']))
            ->assertRedirect(route('login'));

        $this
            ->get(route('settings.security.oauth.callback', ['provider' => 'google']))
            ->assertRedirect(route('login'));
    }

    public function test_oauth_callback_does_not_register_new_user_for_guest(): void
    {
        $existingUser = $this->createUser();

        $this->assertDatabaseCount('users', 1);

        $this
            ->get(route('settings.security.oauth.callback', ['provider' => 'google']))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['id' => $existingUser->id]);
    }

    private function createUser(string $masterPassword = 'MasterPassword123!'): User
    {
        $email = 'oauth.user' . uniqid('', true) . '@example.com';
        $normalizedEmail = strtolower(trim($email));

        return User::query()->create([
            'email' => $email,
            'email_hashed' => hash_hmac('sha256', $normalizedEmail, (string) config('app.key')),
            'password_hash' => Hash::make($masterPassword),
            'first_name' => 'OAuth',
            'last_name' => 'User',
            'private_key_wrapper' => [
                'ciphertext' => 'test-ciphertext',
                'iv' => 'test-iv',
            ],
            'public_key' => 'test-public-key',
            'kdf_salt' => 'test-kdf-salt',
            'kdf_params' => [
                'algorithm' => 'argon2id',
                'opsLimit' => 3,
                'memoryKb' => 65536,
                'type' => 'id',
            ],
            'email_verified' => true,
        ]);
    }
}

