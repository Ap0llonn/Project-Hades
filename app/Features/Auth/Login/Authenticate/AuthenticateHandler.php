<?php

namespace App\Features\Auth\Login\Authenticate;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class AuthenticateHandler
{
    #[CommandHandler]
    public function handle(AuthenticateCommand $command): AuthenticateResult
    {

        $user = User::find($command->userId);

        return new AuthenticateResult(
            $user,
            true,
        );
    }
}
