<?php

namespace App\Features\Auth\Register\StartProcess;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class StartAccountController
{
    public function __invoke(StartAccountRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $startAccountRequest = $request->validated();

        $commandBus->send(new StartAccountCommand(
            $startAccountRequest['email'],
        ));

        return redirect()
            ->route('email.confirmation')
            ->with([
                'email_confirmation_sent' => true,
                'email_confirmation_email' => $startAccountRequest['email'],
            ]);
    }
}
