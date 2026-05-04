<?php

namespace App\Features\Auth\OAuth\Link\Complete;

use App\Features\Auth\OAuth\Support\SupportedOAuthProviders;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

final class CompleteOAuthLinkController
{
    public function __invoke(Request $request, CommandBus $commandBus, string $provider): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $normalizedProvider = SupportedOAuthProviders::normalize($provider);
        if (!SupportedOAuthProviders::isSupported($normalizedProvider)) {
            abort(404);
        }

        try {
            $oauthUser = Socialite::driver($normalizedProvider)
                ->redirectUrl(route('settings.security.oauth.callback', ['provider' => $normalizedProvider]))
                ->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('settings')
                ->with('error', 'Unable to complete OAuth linking. Please try again.');
        }

        try {
            $commandBus->send(new CompleteOAuthLinkCommand(
                userId: (string) $user->id,
                provider: $normalizedProvider,
                providerUserId: (string) ($oauthUser->getId() ?? ''),
                providerEmail: $oauthUser->getEmail(),
                providerName: $oauthUser->getName(),
                providerAvatar: $oauthUser->getAvatar(),
                token: $oauthUser->token ?? null,
                refreshToken: $oauthUser->refreshToken ?? null,
                expiresInSeconds: isset($oauthUser->expiresIn) ? (int) $oauthUser->expiresIn : null,
                metadata: [
                    'linked_via' => 'settings_security',
                    'raw' => is_array($oauthUser->user ?? null) ? $oauthUser->user : null,
                ],
            ));
        } catch (ValidationException $exception) {
            $errors = $exception->errors();
            $message = is_array($errors)
                ? (string) collect($errors)->flatten()->first()
                : 'Unable to complete OAuth linking.';

            return redirect()
                ->route('settings')
                ->with('error', $message !== '' ? $message : 'Unable to complete OAuth linking.');
        }

        $request->session()->flash('oauthPasskeyPrompt', [
            'provider' => $normalizedProvider,
            'started_at' => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('settings')
            ->with('success', ucfirst($normalizedProvider) . ' linked. Register a passkey wrapper now.');
    }
}
