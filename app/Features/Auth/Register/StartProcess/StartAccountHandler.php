<?php

namespace App\Features\Auth\Register\StartProcess;

use App\Features\Auth\EmailValidation\VerificationLinkEmail;
use App\Models\PendingUser;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

final class StartAccountHandler
{
    #[CommandHandler]
    public function handle(StartAccountCommand $command): void
    {
        $startedAtNs = hrtime(true);
        $targetDelayMs = $this->randomTargetDelayMs();

        try {
            $expires_at = now()->addMinutes(15);
            $email = Str::lower(trim($command->email));
            $emailHash = hash_hmac('sha256', $email, (string) config('app.key'));

            $takenEmail = User::where('email_hashed', $emailHash)->exists();

            if ($takenEmail) {
                return;
            }

            $pendingUser = PendingUser::create([
                'email' => $email,
                'expires_at' => $expires_at,
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
        } finally {
            $this->padExecutionTime($startedAtNs, $targetDelayMs);
        }
    }

    private function randomTargetDelayMs(): int
    {
        $min = (int) config('app.start_account_min_delay_ms', 650);
        $max = (int) config('app.start_account_max_delay_ms', 1100);

        if ($max < $min) {
            [$min, $max] = [$max, $min];
        }

        return random_int($min, $max);
    }

    private function padExecutionTime(int $startedAtNs, int $targetDelayMs): void
    {
        $elapsedMs = (hrtime(true) - $startedAtNs) / 1_000_000;
        $remainingMs = $targetDelayMs - $elapsedMs;

        if ($remainingMs > 0) {
            usleep((int) round($remainingMs * 1000));
        }
    }
}
