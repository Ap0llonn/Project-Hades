<?php

namespace App\Features\Auth\OAuth\Login\Start;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class StartOAuthLoginController
{
    public function __invoke(CommandBus $commandBus, string $provider): RedirectResponse
    {
        $result = $commandBus->send(new StartOAuthLoginCommand(
            provider: $provider,
            callbackUrl: route('oauth.login.callback', ['provider' => $provider]),
        ));

        return redirect()->away($result->redirectUrl);
    }
}

