<?php

namespace App\Features\Auth\MFA\TOTP\VerifyChallenge;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class VerifyTotpChallengeController
{
    public function __invoke(VerifyTotpChallengeRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $payload = $request->validated();
        $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');

        if ($pendingUserId === '') {
            return redirect()->route('login');
        }

        $commandBus->send(new VerifyTotpChallengeCommand(
            $pendingUserId,
            $payload['code']
        ));

        $request->session()->put('auth.pending_mfa_verified', true);

        return redirect()->route('login.authenticate');
    }
}
