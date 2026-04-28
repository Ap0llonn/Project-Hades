<?php

namespace App\Features\Auth\MFA\Email\Generate;

use App\Features\Auth\MFA\Email\MfaCodeEmail;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Throwable;

final class GenerateEmailHandler
{
    private const CODE_TTL_SECONDS = 600;

    /**
     * @throws RandomException
     */
    #[CommandHandler]
    public function handle(GenerateEmailCommand $command): GenerateEmailResult
    {
        $user = User::query()->find($command->userId);
        if (!$user) {
            throw ValidationException::withMessages([
                'code' => 'Unable to send verification email. Please sign in again.',
            ]);
        }

        $now = now()->timestamp;
        $existingState = $this->normalizeState($command->existingVerificationState, $now);
        if ($existingState !== null) {
            return new GenerateEmailResult($existingState, false);
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

        return new GenerateEmailResult($nextState, true);
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

