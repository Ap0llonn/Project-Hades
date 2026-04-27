<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureVaultDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MfaFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureVaultDomain::class);
    }

    public function test_email_verify_redirects_to_recovery_codes_when_first_method_is_activated(): void
    {
        $user = $this->createUser();
        $code = '123456';

        $response = $this
            ->actingAs($user)
            ->withSession([
                'mfa.email_verification' => [
                    'code_hash' => hash_hmac('sha256', $code, (string) config('app.key')),
                    'expires_at' => now()->addMinutes(10)->timestamp,
                    'sent_at' => now()->timestamp,
                ],
            ])
            ->post(route('mfa.email.verify'), [
                'code' => $code,
            ]);

        $response->assertRedirect(route('mfa.recovery-codes'));
        $response->assertSessionMissing('mfa.email_verification');

        $this->assertDatabaseHas('mfa_methods', [
            'user_id' => $user->id,
            'email_enabled' => true,
            'recovery_codes_show' => true,
        ]);
    }

    public function test_email_setup_does_not_resend_when_a_valid_code_is_already_pending(): void
    {
        Mail::fake();

        $user = $this->createUser();
        $existingState = [
            'code_hash' => hash_hmac('sha256', '654321', (string) config('app.key')),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'sent_at' => now()->subSeconds(5)->timestamp,
        ];

        $response = $this
            ->actingAs($user)
            ->withSession(['mfa.email_verification' => $existingState])
            ->post(route('mfa.email.generate'));

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('mfa.email_verification', function ($state) use ($existingState): bool {
            return is_array($state)
                && ($state['code_hash'] ?? null) === $existingState['code_hash']
                && ($state['expires_at'] ?? null) === $existingState['expires_at'];
        });

        Mail::assertNothingSent();
    }

    public function test_login_challenge_does_not_resend_without_force_when_code_is_pending(): void
    {
        Mail::fake();

        $user = $this->createUser();
        $user->mfa()->update([
            'email_enabled' => true,
            'mfa_activated' => true,
        ]);

        $existingState = [
            'code_hash' => hash_hmac('sha256', '111111', (string) config('app.key')),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'sent_at' => now()->timestamp,
        ];

        $response = $this
            ->withSession([
                'auth.pending_user_id' => (string) $user->id,
                'auth.pending_started_at' => time(),
                'auth.pending_mfa_verified' => false,
                'auth.pending_email_mfa' => $existingState,
            ])
            ->post(route('mfa.email.request-challenge'));

        $response->assertRedirect(route('mfa'));
        $response->assertSessionHas('auth.pending_email_mfa', function ($state) use ($existingState): bool {
            return is_array($state)
                && ($state['code_hash'] ?? null) === $existingState['code_hash']
                && ($state['expires_at'] ?? null) === $existingState['expires_at'];
        });

        Mail::assertNothingSent();
    }

    public function test_login_challenge_resend_enforces_cooldown(): void
    {
        Mail::fake();

        $user = $this->createUser();
        $user->mfa()->update([
            'email_enabled' => true,
            'mfa_activated' => true,
        ]);

        $response = $this
            ->withSession([
                'auth.pending_user_id' => (string) $user->id,
                'auth.pending_started_at' => time(),
                'auth.pending_mfa_verified' => false,
                'auth.pending_email_mfa' => [
                    'code_hash' => hash_hmac('sha256', '111111', (string) config('app.key')),
                    'expires_at' => now()->addMinutes(10)->timestamp,
                    'sent_at' => now()->timestamp,
                ],
            ])
            ->from(route('mfa'))
            ->post(route('mfa.email.request-challenge'), [
                'force' => true,
            ]);

        $response->assertRedirect(route('mfa'));
        $response->assertSessionHasErrors('code');
        Mail::assertNothingSent();
    }

    public function test_disabling_totp_keeps_mfa_active_when_email_method_is_enabled(): void
    {
        $masterPassword = 'MasterPassword123!';
        $user = $this->createUser($masterPassword);
        $user->mfa()->update([
            'totp_enabled' => true,
            'totp_secret' => encrypt('JBSWY3DPEHPK3PXP'),
            'email_enabled' => true,
            'mfa_activated' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('mfa.totp.disable'), [
                'masterPassword' => $masterPassword,
            ]);

        $response->assertRedirect(route('settings'));

        $mfa = $user->mfa()->firstOrFail();
        $this->assertFalse((bool) $mfa->totp_enabled);
        $this->assertNull($mfa->totp_secret);
        $this->assertTrue((bool) $mfa->email_enabled);
        $this->assertTrue((bool) $mfa->mfa_activated);
    }

    public function test_verify_challenge_endpoint_is_rate_limited(): void
    {
        $user = $this->createUser();
        $user->mfa()->update([
            'email_enabled' => true,
            'mfa_activated' => true,
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $response = $this
                ->withSession([
                    'auth.pending_user_id' => (string) $user->id,
                    'auth.pending_started_at' => time(),
                    'auth.pending_mfa_verified' => false,
                ])
                ->from(route('mfa'))
                ->post(route('mfa.totp.verify-challenge'), [
                    'method' => 'totp',
                    'code' => '000000',
                ]);

            $response->assertRedirect(route('mfa'));
            $response->assertSessionHasErrors('code');
        }

        $limitedResponse = $this
            ->withSession([
                'auth.pending_user_id' => (string) $user->id,
                'auth.pending_started_at' => time(),
                'auth.pending_mfa_verified' => false,
            ])
            ->from(route('mfa'))
            ->post(route('mfa.totp.verify-challenge'), [
                'method' => 'totp',
                'code' => '000000',
            ]);

        $limitedResponse->assertRedirect(route('mfa'));
        $limitedResponse->assertSessionHasErrors('code');
        $this->assertSame(
            'Too many MFA verification attempts. Please wait a minute and try again.',
            session('errors')->first('code'),
        );
    }

    private function createUser(string $masterPassword = 'MasterPassword123!'): User
    {
        $email = 'user' . uniqid('', true) . '@example.com';
        $normalizedEmail = strtolower(trim($email));

        return User::query()->create([
            'email' => $email,
            'email_hashed' => hash_hmac('sha256', $normalizedEmail, (string) config('app.key')),
            'password_hash' => Hash::make($masterPassword),
            'first_name' => 'Sam',
            'last_name' => 'Doe',
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
