<?php

namespace App\Features\Auth\Passkey\Create\Store;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

class StorePasskeyController
{
    public function __invoke(StorePasskeyRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $payload = $request->validated();

        $user = $request->user();
        abort_if($user === null, 403);

        $commandBus->send(new StorePasskeyCommand(
            userId: (string) $user->id,
            passkeyJson: $payload['passkey'],
            passkeyOptionsJson: $payload['options'],
            hostName: (string) $request->getHost(),
            appUrl: (string) $request->getSchemeAndHttpHost(),
            name: $payload['name'] ?? null,
        ));

        return redirect()->route('settings')->with('success', 'Passkey added.');
    }
}
