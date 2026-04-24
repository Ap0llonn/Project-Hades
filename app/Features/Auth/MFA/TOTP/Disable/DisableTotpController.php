<?php

namespace App\Features\Auth\MFA\TOTP\Disable;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class DisableTotpController
{
    public function __invoke(DisableTotpRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $payload = $request->validated();

        $commandBus->send(new DisableTotpCommand(
            $user->id,
            $payload['masterPassword']
        ));

        return redirect()->route('settings')->with('success', 'Authenticator app removed.');
    }
}
