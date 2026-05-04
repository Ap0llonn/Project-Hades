<?php

namespace App\Features\Auth\OAuth\Link\Start;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StartOAuthLinkController
{
    public function __invoke(Request $request, CommandBus $commandBus, string $provider): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $result = $commandBus->send(new StartOAuthLinkCommand(
            provider: $provider,
            callbackUrl: route('settings.security.oauth.callback', ['provider' => $provider]),
        ));

        return redirect()->away($result->redirectUrl);
    }
}

