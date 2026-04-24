<?php

namespace App\Features\Auth\MFA\TOTP\Disable;

use App\Models\MfaMethods;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

final class DisableTotpHandler
{
    #[CommandHandler]
    public function handle(DisableTotpCommand $command): void
    {
        $user = User::query()->find($command->userId);
        if (!$user || !Hash::check($command->masterPassword, $user->password_hash)) {
            throw ValidationException::withMessages([
                'masterPassword' => 'Master password is incorrect.',
            ]);
        }

        $mfaMethods = MfaMethods::query()->where('user_id', $command->userId)->first();
        if (!$mfaMethods) {
            return;
        }

        $mfaMethods->totp_secret = null;
        $mfaMethods->mfa_activated = false;

        if (Schema::hasColumn('mfa_methods', 'totp_enabled') && array_key_exists('totp_enabled', $mfaMethods->getAttributes())) {
            $mfaMethods->totp_enabled = false;
        }

        $mfaMethods->save();
    }
}
