<?php

namespace App\Features\Auth\Passkey\Create\Start;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\Response;

class CreateStartPasskeyController
{
    public function __invoke(CreateStartPasskeyRequest $request, CommandBus $commandBus): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $result = $commandBus->send(new CreateStartPasskeyCommand(
            userId: (string) $user->id,
            hostName: (string) $request->getHost(),
            appUrl: (string) $request->getSchemeAndHttpHost(),
        ));

        return response($result->optionsJson, 200, ['Content-Type' => 'application/json']);
    }
}
