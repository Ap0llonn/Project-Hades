<?php

namespace App\Features\Auth\OAuth\Link\Complete;

use App\Features\Auth\OAuth\Support\SupportedOAuthProviders;
use App\Models\OAuthAccount;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

final class CompleteOAuthLinkHandler
{
    #[CommandHandler]
    public function handle(CompleteOAuthLinkCommand $command): void
    {
        $provider = SupportedOAuthProviders::normalize($command->provider);
        if (!SupportedOAuthProviders::isSupported($provider)) {
            throw ValidationException::withMessages([
                'provider' => 'Unsupported OAuth provider.',
            ]);
        }

        $providerUserId = trim($command->providerUserId);
        if ($providerUserId === '') {
            throw ValidationException::withMessages([
                'provider' => 'OAuth provider user id is missing.',
            ]);
        }

        $linkedElsewhere = OAuthAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->whereNull('unlinked_at')
            ->where('user_id', '!=', $command->userId)
            ->exists();

        if ($linkedElsewhere) {
            throw ValidationException::withMessages([
                'provider' => 'This OAuth account is already linked to another user.',
            ]);
        }

        $account = OAuthAccount::query()->firstOrNew([
            'user_id' => $command->userId,
            'provider' => $provider,
        ]);

        $account->provider_user_id = $providerUserId;
        $account->provider_email = $command->providerEmail;
        $account->provider_name = $command->providerName;
        $account->provider_avatar = $command->providerAvatar;
        $account->token = $command->token;
        $account->refresh_token = $command->refreshToken;
        $account->token_expires_at = $command->expiresInSeconds !== null
            ? Carbon::now()->addSeconds($command->expiresInSeconds)
            : null;
        $account->metadata = array_merge($command->metadata, [
            'requires_passkey_setup' => true,
        ]);
        $account->linked_at = now();
        $account->unlinked_at = null;
        $account->save();
    }
}

