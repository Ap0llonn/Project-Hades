<?php

namespace App\Features\Dashboard\Settings\ChangePassword;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class ChangePasswordHandler
{
    #[CommandHandler]
    public function handle(ChangePasswordCommand $command): void
    {
        /** @var User $user */
        $user = User::query()->findOrFail($command->userId);

        if (!Hash::check($command->currentPassword, $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        if (Hash::check($command->newPassword, $user->password_hash)) {
            throw ValidationException::withMessages([
                'password' => ['New password must be different from your current password.'],
            ]);
        }

        $user->password_hash = Hash::make($command->newPassword);
        $user->save();
    }
}
