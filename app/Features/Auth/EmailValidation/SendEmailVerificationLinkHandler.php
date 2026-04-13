<?php

namespace App\Features\Auth\EmailValidation;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

final class SendEmailVerificationLinkHandler
{
    #[CommandHandler]
    public function handle(SendEmailVerificationLinkCommand $command): void
    {
        $email = Str::lower(trim($command->email));
        $user = User::query()
            ->where('email', $email)
            ->firstOrFail();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        Mail::to($user->email)->queue(new VerificationLinkEmail(
            firstName: $user->first_name,
            verificationUrl: $verificationUrl,
            illustrationUrl: rtrim((string) config('app.url'), '/').'/images/email-confirmation.svg',
        ));
    }
}
