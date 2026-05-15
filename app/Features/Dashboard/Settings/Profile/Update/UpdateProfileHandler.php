<?php

namespace App\Features\Dashboard\Settings\Profile\Update;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;

final class UpdateProfileHandler
{
    #[CommandHandler]
    public function handle(UpdateProfileCommand $command): ?User
    {
        $user = User::query()->where('id', $command->userId)->first();
        if ($user === null) {
            return null;
        }

        $user->first_name = $command->firstName;
        $user->last_name = $command->lastName;
        $user->save();

        return $user->fresh();
    }
}
