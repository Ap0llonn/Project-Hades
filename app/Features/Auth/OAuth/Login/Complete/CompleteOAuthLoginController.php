<?php

namespace App\Features\Auth\OAuth\Login\Complete;

use App\Features\Auth\OAuth\Support\SupportedOAuthProviders;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

final class CompleteOAuthLoginController
{
    public function __invoke(Request $request, CommandBus $commandBus, string $provider): RedirectResponse
    {
        $normalizedProvider = SupportedOAuthProviders::normalize($provider);
        if (!SupportedOAuthProviders::isSupported($normalizedProvider)) {
            return redirect()->route('login');
        }

        try {
            $oauthUser = Socialite::driver($normalizedProvider)
                ->redirectUrl(route('oauth.login.callback', ['provider' => $normalizedProvider]))
                ->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->with('error', 'OAuth login failed. Please try again.');
        }

        $result = $commandBus->send(new CompleteOAuthLoginCommand(
            provider: $normalizedProvider,
            providerUserId: (string) ($oauthUser->getId() ?? ''),
        ));

        if ($result->userId === null || $result->userId === '') {
            return redirect()->route('login')->with('error', 'No linked account found for this OAuth profile.');
        }

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
            'auth.pending_user_id' => $result->userId,
            'auth.pending_started_at' => time(),
            'auth.pending_mfa_verified' => !$result->mfaActivated,
            'auth.pending_primary_method' => 'oauth',
        ]);

        if ($result->mfaActivated) {
            return redirect()->route('mfa');
        }

        return redirect()->route('login.authenticate');
    }
}

