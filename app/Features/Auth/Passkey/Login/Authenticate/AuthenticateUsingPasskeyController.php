<?php

namespace App\Features\Auth\Passkey\Login\Authenticate;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticateUsingPasskeyController
{
    public function __invoke(AuthenticateUsingPasskeyRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $passkeyAuthenticationOptions = (string) Session::get('passkey-authentication-options', '');
        if ($passkeyAuthenticationOptions === '') {
            return back()->withErrors([
                'passkey' => __('passkeys::passkeys.invalid'),
            ]);
        }

        $payload = $request->validated();

        $result = $commandBus->send(new AuthenticateUsingPasskeyCommand(
            startAuthenticationResponseJson: $payload['start_authentication_response'],
            passkeyAuthenticationOptionsJson: $passkeyAuthenticationOptions,
            hostName: (string) $request->getHost(),
            appUrl: (string) $request->getSchemeAndHttpHost(),
        ));

        if (!$result->user) {
            return back()->withErrors([
                'passkey' => __('passkeys::passkeys.invalid'),
            ]);
        }

        Auth::login($result->user, false);
        $request->session()->regenerate();
        $request->session()->put('auth.primary_method', 'passkey');
        $request->session()->put('auth.mfa_method', '');
        $request->session()->forget([
            'auth.pending_user_id',
            'auth.pending_started_at',
            'auth.pending_mfa_verified',
            'auth.pending_email_mfa',
            'auth.pending_primary_method',
            'auth.pending_mfa_method',
        ]);

        return redirect()->intended(route('dashboard'));
    }
}
