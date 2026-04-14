<?php

namespace Tests\Feature\Auth;

use App\Features\Auth\EmailValidation\VerificationLinkEmail;
use App\Features\Auth\Register\StartProcess\StartAccountCommand;
use App\Features\Auth\Register\StartProcess\StartAccountHandler;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_account_command_sends_verification_email(): void
    {
        Mail::fake();

        $user = User::create([
            'email' => 'new.user@example.com',
            'email_hashed' => hash_hmac('sha256', 'new.user@example.com', (string) config('app.key')),
            'first_name' => 'Sam',
            'last_name' => 'Tremblay',
            'master_key_wrapper' => [
                'ciphertext' => 'test-ciphertext',
                'iv' => 'test-iv',
            ],
            'kdf_salt' => 'test-salt',
            'kdf_params' => [
                'algorithm' => 'argon2id',
                'opsLimit' => 3,
                'memoryKb' => 65536,
                'type' => 'id',
            ],
        ]);

        app(StartAccountHandler::class)->handle(new StartAccountCommand($user->email));

        Mail::assertQueued(VerificationLinkEmail::class, function (VerificationLinkEmail $mail): bool {
            $verificationPath = parse_url($mail->verificationUrl, PHP_URL_PATH);

            return $mail->hasTo('new.user@example.com')
                && $verificationPath === '/email/verify';
        });
    }

    public function test_verify_link_redirects_to_finish_account_and_stores_verification_session(): void
    {
        $pendingUser = PendingUser::create([
            'email' => 'pending.user@example.com',
            'expires_at' => now()->addMinutes(10),
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            ['id' => $pendingUser->id],
        );

        $verifyResponse = $this->get($verificationUrl);

        $verifyResponse->assertRedirect(route('finish-account'));
        $verifyResponse->assertSessionHas('verified_signup_email', 'pending.user@example.com');
        $verifyResponse->assertSessionHas('verified_pending_user_id', $pendingUser->id);
    }
}
