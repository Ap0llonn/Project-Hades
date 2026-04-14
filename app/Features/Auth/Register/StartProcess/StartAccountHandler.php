<?php

namespace App\Features\Auth\Register\StartProcess;

use App\Features\Auth\EmailValidation\VerificationLinkEmail;
use App\Models\PendingUser;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

final class StartAccountHandler
{
    #[CommandHandler]
    public function handle(StartAccountCommand $command): void
    {
        $expires_at = now()->addMinutes(15);
        $email = Str::lower(trim($command->email));

        $pendingUser = PendingUser::create([
            "email" => $email,
            "expires_at" => $expires_at,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            $expires_at,
            [
                'id' => $pendingUser->id,
            ],
        );

        Mail::to($email)->queue(new VerificationLinkEmail(
            verificationUrl: $verificationUrl
        ));
    }
}
