<?php

namespace App\Features\Auth\MFA\Email\Disable;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class DisableEmailController
{
    public function __invoke(DisableEmailRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $payload = $request->validated();

        $commandBus->send(new DisableEmailCommand(
            $user->id,
            $payload['masterPassword'],
        ));

        return redirect()->route('settings')->with('success', 'Email MFA removed.');
    }
}

