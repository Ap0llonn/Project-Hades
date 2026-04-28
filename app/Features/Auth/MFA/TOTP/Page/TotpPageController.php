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
        $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');

        if ($safeRedirectTo !== null) {
            $request->session()->put('url.intended', $safeRedirectTo);
        }

        $challenge = $commandBus->send(new TotpPageCommand($pendingUserId, $safeRedirectTo));

        return Inertia::render('auth/mfa/pages/MfaValidationPage', [
            'challenge' => $challenge,
        ]);
    }
}
