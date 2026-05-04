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
            'auth.pending_primary_method',
            'auth.pending_mfa_method',
            'auth.primary_method',
            'auth.mfa_method',
            'auth.passkey_credential_id',
            'auth.passkey_id',
        ]);

        $request->session()->put([
            'auth.pending_user_id' => $identifyResult->userId,
            'auth.pending_started_at' => time(),
            'auth.pending_mfa_verified' => !$identifyResult->mfaActivated,
            'auth.pending_primary_method' => 'password',
        ]);

        if ($identifyResult->mfaActivated) {
            return redirect()->route('mfa');
        }

        return redirect()->route('login.authenticate');
    }
}
