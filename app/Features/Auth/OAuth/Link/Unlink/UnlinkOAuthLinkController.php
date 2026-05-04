<?php

namespace App\Features\Auth\OAuth\Link\Unlink;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UnlinkOAuthLinkController
{
    public function __invoke(Request $request, CommandBus $commandBus, string $provider): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $commandBus->send(new UnlinkOAuthLinkCommand(
            userId: (string) $user->id,
            provider: $provider,
        ));

        return redirect()->route('settings')->with('success', 'OAuth account unlinked.');
    }
}

