<?php

namespace App\Features\Auth\MFA\RecoveryTokens;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class RecoveryCodeController
{
    public function page(): Response
    {
        return Inertia::render('Auth/RecoveryCode/Page');
    }

    public function initCodes(RecoveryCodeRequest $request, CommandBus $commandBus): RedirectResponse
    {
        Log::info('im here');
        $payload = $request->validated();
        $commandBus->send(new RecoveryCodeCommand($payload['recoveryCodes']));

        return redirect()->route('dashboard');
    }
}
