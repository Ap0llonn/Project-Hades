<?php

namespace App\Features\Auth\MFA\Email\Disable;

use App\Models\MfaMethods;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class DisableEmailHandler
{
    #[CommandHandler]
    public function handle(DisableEmailCommand $command): void
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

        $mfaMethods->email_enabled = false;
        $mfaMethods->save();
    }
}

