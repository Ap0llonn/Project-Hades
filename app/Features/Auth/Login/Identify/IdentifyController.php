<?php

namespace App\Features\Auth\Login\Identify;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class IdentifyController
{
    public function __invoke(IdentifyRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $identifyResult = $commandBus->send(new IdentifyCommand(
            $request->email,
            $request->password
        ));

        $request->session()->forget([
            'auth.pending_user_id',
            'auth.pending_started_at',
            'auth.pending_mfa_verified',
            'auth.pending_email_mfa',
        ]);

        $request->session()->put([
            'auth.pending_user_id' => $identifyResult->userId,
            'auth.pending_started_at' => time(),
            'auth.pending_mfa_verified' => !$identifyResult->mfaActivated,
        ]);

        if ($identifyResult->mfaActivated) {
            return redirect()->route('mfa');
        }

        return redirect()->route('login.authenticate');
    }
}
