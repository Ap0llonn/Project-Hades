<?php

namespace App\Features\Auth\MFA\TOTP\Page;

use Ecotone\Modelling\CommandBus;
use Inertia\Inertia;
use Inertia\Response;

final class TotpPageController
{
    public function __invoke(TotpPageRequest $request, CommandBus $commandBus): Response
    {
        $payload = $request->validated();
        $safeRedirectTo = $payload['redirect'] ?? null;

        if ($safeRedirectTo !== null) {
            $request->session()->put('url.intended', $safeRedirectTo);
        }

        $commandBus->send(new TotpPageCommand($safeRedirectTo));

        return Inertia::render('auth/mfa/pages/MfaValidationPage');
    }
}
