<?php

namespace App\Features\Auth\Passkey\Login\GenerateOptions;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\Response;

class GeneratePasskeyAuthenticationOptionsController
{
    public function __invoke(\Illuminate\Http\Request $request, CommandBus $commandBus): Response
    {
        $result = $commandBus->send(new GeneratePasskeyAuthenticationOptionsCommand(
            hostName: (string) $request->getHost(),
            appUrl: (string) $request->getSchemeAndHttpHost(),
        ));

        return response($result->optionsJson, 200, ['Content-Type' => 'application/json']);
    }
}
