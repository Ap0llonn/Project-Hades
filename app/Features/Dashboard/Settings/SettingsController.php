<?php

namespace App\Features\Dashboard\Settings;

use App\Features\Auth\OAuth\List\FetchOAuthLinksHandler;
use App\Features\Auth\OAuth\List\FetchOAuthLinksQuery;
use App\Features\Dashboard\Settings\Sessions\Read\ListActiveSessionsHandler;
use App\Features\Dashboard\Settings\Sessions\Read\ListActiveSessionsQuery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelPasskeys\Models\Passkey;

class SettingsController
{
    public function __invoke(
        Request $request,
        FetchOAuthLinksHandler $fetchOAuthLinksHandler,
        ListActiveSessionsHandler $listActiveSessionsHandler,
    ): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        $passkeys = [];

        if (
            $user !== null
            && Schema::hasTable('passkeys')
            && class_exists(Passkey::class)
        ) {
            $passkeys = $user
                ->passkeys()
                ->orderByDesc('last_used_at')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (Passkey $passkey): array => [
                    'id' => (string) ($passkey->uuid ?? $passkey->id),
                    'name' => $passkey->name,
                    'created_at' => optional($passkey->created_at)?->toIso8601String(),
                    'last_used_at' => optional($passkey->last_used_at)?->toIso8601String(),
                ])
                ->all();
        }

        $oauthProviders = $user !== null
            ? $fetchOAuthLinksHandler
                ->handle(new FetchOAuthLinksQuery((string) $user->id))
                ->providers
            : [];

        $oauthPasskeyPrompt = $request->session()->get('oauthPasskeyPrompt');
        if ($oauthPasskeyPrompt === null) {
            $pendingProvider = collect($oauthProviders)->first(fn (array $provider): bool => (bool) ($provider['requires_passkey_setup'] ?? false));
            if ($pendingProvider !== null) {
                $oauthPasskeyPrompt = [
                    'provider' => (string) ($pendingProvider['key'] ?? ''),
                    'started_at' => now()->toIso8601String(),
                ];
            }
        }

        $sessions = $user !== null
            ? $listActiveSessionsHandler->handle(new ListActiveSessionsQuery(
                userId: (string) $user->id,
                currentSessionId: (string) $request->session()->getId(),
            ))
            : [];

        return Inertia::render('dashboard/settings/pages/SettingsPage', [
            'profile' => [
                'email' => (string) ($user?->email ?? ''),
                'first_name' => (string) ($user?->first_name ?? ''),
                'last_name' => (string) ($user?->last_name ?? ''),
            ],
            'security' => [
                'mfa_activated' =>  $user->mfa->mfa_activated ?? false,
                'totp_enabled' => $user->mfa->totp_enabled ?? false,
                'email_enabled' => $user->mfa->email_enabled ?? false,
                'passkeys' => $passkeys,
                'oauth_providers' => $oauthProviders,
                'oauth_passkey_prompt' => $oauthPasskeyPrompt,
            ],
            'sessions' => $sessions,
        ]);
    }
}
