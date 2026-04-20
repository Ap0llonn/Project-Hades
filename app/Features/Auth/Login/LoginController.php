<?php

namespace App\Features\Auth\Login;

use App\Models\MfaMethods;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class LoginController
{
    public function __invoke(LoginRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $commandBus->send(new LoginCommand(
            $request->email,
            $request->password
        ));

        $user = Auth::user();
        $userMfa = $user->mfa;

        $request->session()->regenerate();
        if ($userMfa->mfa_activated){
            return redirect()->route('mfa');
        }

        return redirect()->intended('dashboard');
    }
}
