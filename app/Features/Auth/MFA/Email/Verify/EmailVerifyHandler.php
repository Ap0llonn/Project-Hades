<?php

namespace App\Features\Auth\MFA\Email\Verify;

use App\Models\MfaMethods;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Validation\ValidationException;

final class EmailVerifyHandler
{
    #[CommandHandler]
    public function handle(EmailVerifyCommand $command): void
    {
        $verificationState = $command->verificationState;
        if (!is_array($verificationState)) {
            throw ValidationException::withMessages([
                'code' => 'No verification code is pending. Request a new one.',
            ]);
        }

        $expiresAt = (int) ($verificationState['expires_at'] ?? 0);
        if ($expiresAt <= 0 || now()->timestamp > $expiresAt) {
            throw ValidationException::withMessages([
                'code' => 'Verification code expired. Request a new one.',
            ]);
        }

        $normalizedCode = preg_replace('/\D+/', '', $command->code);
        if ($normalizedCode === null || strlen($normalizedCode) !== 6) {
            throw ValidationException::withMessages([
                'code' => 'Verification code must be exactly 6 digits.',
            ]);
        }

        $expectedHash = (string) ($verificationState['code_hash'] ?? '');
        $providedHash = hash_hmac('sha256', $normalizedCode, (string) config('app.key'));
        if ($expectedHash === '' || !hash_equals($expectedHash, $providedHash)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid verification code.',
            ]);
        }

        $mfaMethods = MfaMethods::query()->where('user_id', $command->userId)->first();
        if (!$mfaMethods) {
            throw ValidationException::withMessages([
                'code' => 'Unable to verify email MFA setup.',
            ]);
        }

        $mfaMethods->email_enabled = true;
        $mfaMethods->save();
    }
}
