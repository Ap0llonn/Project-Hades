<?php

namespace App\Features\Auth\MFA\Email\Challenge;

use App\Features\Auth\MFA\Email\MfaCodeEmail;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Throwable;

final class RequestEmailChallengeHandler
{
    private const CODE_TTL_SECONDS = 600;
    private const RESEND_COOLDOWN_SECONDS = 30;

    /**
     * @throws RandomException
     */
    #[CommandHandler]
    public function handle(RequestEmailChallengeCommand $command): RequestEmailChallengeResult
    {
        $user = User::query()->with('mfa')->find($command->userId);
        if (!$user || !$user->mfa?->mfa_activated || !$user->mfa?->email_enabled) {
            throw ValidationException::withMessages([
                'code' => 'Email verification is not enabled for this account.',
            ]);
        }

        $now = now()->timestamp;
        $currentState = $this->normalizeState($command->existingVerificationState, $now);

        if ($currentState !== null) {
            if (!$command->force) {
                return new RequestEmailChallengeResult($currentState, false);
            }

            $sentAt = (int) ($currentState['sent_at'] ?? 0);
            $secondsSinceLastSend = $now - $sentAt;
            if ($sentAt > 0 && $secondsSinceLastSend < self::RESEND_COOLDOWN_SECONDS) {
                $secondsLeft = self::RESEND_COOLDOWN_SECONDS - $secondsSinceLastSend;

                throw ValidationException::withMessages([
                    'code' => "Please wait {$secondsLeft} seconds before requesting another email code.",
                ]);
            }
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $nextState = [
            'code_hash' => hash_hmac('sha256', $code, (string) config('app.key')),
            'expires_at' => $now + self::CODE_TTL_SECONDS,
            'sent_at' => $now,
        ];

        try {
            Mail::to($user->email)->send(new MfaCodeEmail(
                code: $code,
                expiresInMinutes: (int) ceil(self::CODE_TTL_SECONDS / 60),
            ));
        } catch (Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'code' => 'Unable to send verification email. Please try again.',
            ]);
        }

        return new RequestEmailChallengeResult($nextState, true);
    }

    private function normalizeState(?array $state, int $now): ?array
    {
        if (!is_array($state)) {
            return null;
        }

        $hash = (string) ($state['code_hash'] ?? '');
        $expiresAt = (int) ($state['expires_at'] ?? 0);
        if ($hash === '' || $expiresAt <= $now) {
            return null;
        }

        return [
            'code_hash' => $hash,
            'expires_at' => $expiresAt,
            'sent_at' => (int) ($state['sent_at'] ?? 0),
        ];
    }
}

