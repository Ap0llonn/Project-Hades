<?php

namespace App\Features\Auth\Register\StartProcess;

use Ecotone\Modelling\CommandBus;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

final class StartAccountController
{
    public function __invoke(StartAccountRequest $request, CommandBus $commandBus): Response
    {
        $startAccountRequest = $request->validated();

        $commandBus->send(new StartAccountCommand(
            $startAccountRequest['email'],
        ));

        return Inertia::location(route('email.confirmation', [
            'email' => $startAccountRequest['email'],
        ]));
    }
}
