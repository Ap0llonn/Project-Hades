<?php

namespace App\Features\Auth\Login;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginHandler
{

    #[CommandHandler]
    public function handle(LoginCommand $command): void
    {

    }
}
