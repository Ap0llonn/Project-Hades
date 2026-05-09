<?php

namespace App\Features\Auth\OAuth\Login\Complete;

use App\Features\Auth\OAuth\Support\SupportedOAuthProviders;
use App\Models\OAuthAccount;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Schema;

final class CompleteOAuthLoginHandler
{
    #[CommandHandler]
    public function handle(CompleteOAuthLoginCommand $command): CompleteOAuthLoginResult
    {
        $provider = SupportedOAuthProviders::normalize($command->provider);
        if (!SupportedOAuthProviders::isSupported($provider) || !Schema::hasTable('oauth_accounts')) {
            return new CompleteOAuthLoginResult(null, false);
        }

        $providerUserId = trim($command->providerUserId);
        if ($providerUserId === '') {
            return new CompleteOAuthLoginResult(null, false);
        }

        $account = OAuthAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->whereNull('unlinked_at')
            ->first();

        $user = $account?->user;
        if ($user === null) {
            return new CompleteOAuthLoginResult(null, false);
        }

        return new CompleteOAuthLoginResult(
            userId: (string) $user->id,
            mfaActivated: (bool) ($user->mfa?->mfa_activated ?? false),
        );
    }
}

