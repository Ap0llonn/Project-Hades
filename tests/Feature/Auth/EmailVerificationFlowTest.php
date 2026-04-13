<?php

namespace Tests\Feature\Auth;

use App\Features\Auth\EmailValidation\SendEmailVerificationLinkCommand;
use App\Features\Auth\EmailValidation\SendEmailVerificationLinkHandler;
use App\Features\Auth\EmailValidation\VerificationLinkEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_email_verification_link_command_sends_verification_email(): void
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

        app(SendEmailVerificationLinkHandler::class)->handle(new SendEmailVerificationLinkCommand($user->email));

        Mail::assertQueued(VerificationLinkEmail::class, function (VerificationLinkEmail $mail): bool {
            return $mail->hasTo('new.user@example.com')
                && str_contains($mail->verificationUrl, '/email/verify/');
        });
    }
}
