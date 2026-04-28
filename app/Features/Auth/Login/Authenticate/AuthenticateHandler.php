<?php

namespace App\Features\Auth\Login\Authenticate;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;

final class AuthenticateHandler
{
    #[CommandHandler]
    public function handle(AuthenticateCommand $command): AuthenticateResult
    {
        $user = User::find($command->userId);

        return new AuthenticateResult($user);
    }
}
