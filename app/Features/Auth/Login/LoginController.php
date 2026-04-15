<?php

namespace App\Features\Auth\Login;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class LoginController
{
    public function __invoke(LoginRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $commandBus->send(new LoginCommand(
            $request->email,
            $request->password
        ));

        return redirect()->route('dashboard');
    }
}
