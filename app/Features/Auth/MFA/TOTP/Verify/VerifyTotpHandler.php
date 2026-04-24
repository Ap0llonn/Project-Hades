<?php

namespace App\Features\Auth\MFA\TOTP\Verify;

use App\Models\MfaMethods;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use OTPHP\TOTP;

final class VerifyTotpHandler
{
    #[CommandHandler]
    public function handle(VerifyTotpCommand $command): void
    {
        $mfaMethods = MfaMethods::query()->where('user_id', $command->userId)->first();
        if (!$mfaMethods || !$mfaMethods->totp_secret) {
            throw ValidationException::withMessages([
                'code' => 'MFA is not enabled.',
            ]);
        }

        $code = preg_replace('/\D+/', '', $command->code);
        if ($code === null || strlen($code) !== 6) {
            throw ValidationException::withMessages([
                'code' => 'Verification code must be exactly 6 digits.',
            ]);
        }

        $secret = trim((string) $mfaMethods->totp_secret);
        try {
            $secret = (string) decrypt($secret);
        } catch (DecryptException) {
        }

        $totp = TOTP::create($secret);
        if (!$totp->verify($code, null, 29)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid code. Check your device time and try the latest 6-digit code.',
            ]);
        }

        $mfaMethods->mfa_activated = true;
        if (Schema::hasColumn('mfa_methods', 'totp_enabled') && array_key_exists('totp_enabled', $mfaMethods->getAttributes())) {
            $mfaMethods->totp_enabled = true;
        }

        $mfaMethods->save();
    }
}
