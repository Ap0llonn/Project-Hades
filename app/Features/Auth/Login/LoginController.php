<?php

namespace App\Features\Auth\Login;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class LoginController
{
    public function __invoke(LoginRequest $request, CommandBus $commandBus): RedirectResponse
    {

        return redirect()->route('home');
    }
}
