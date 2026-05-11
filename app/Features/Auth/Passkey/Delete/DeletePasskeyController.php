<?php

namespace App\Features\Auth\Passkey\Delete;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeletePasskeyController
{
    public function __invoke(Request $request, CommandBus $commandBus, string $passkeyId): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $commandBus->send(new DeletePasskeyCommand(
            userId: (string) $user->id,
            passkeyId: $passkeyId,
        ));

        return redirect()->route('settings')->with('success', 'Passkey removed.');
    }
}
