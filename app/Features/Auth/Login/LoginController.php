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
        $userMfa = MfaMethods::find($user->id);
        if (!$userMfa->recovery_codes_show) {
            $userMfa->update([
                'recovery_codes_show' => true,
            ]);
            return redirect()->route('mfa.recovery-codes');
        }
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }
}
