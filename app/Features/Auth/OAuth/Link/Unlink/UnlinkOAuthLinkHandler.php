<?php

namespace App\Features\Auth\OAuth\Link\Unlink;

use App\Features\Auth\OAuth\Support\SupportedOAuthProviders;
use App\Models\OAuthAccount;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Validation\ValidationException;

final class UnlinkOAuthLinkHandler
{
    #[CommandHandler]
    public function handle(UnlinkOAuthLinkCommand $command): void
    {
        $provider = SupportedOAuthProviders::normalize($command->provider);
        if (!SupportedOAuthProviders::isSupported($provider)) {
            throw ValidationException::withMessages([
                'provider' => 'Unsupported OAuth provider.',
            ]);
        }

        OAuthAccount::query()
            ->where('user_id', $command->userId)
            ->where('provider', $provider)
            ->whereNull('unlinked_at')
            ->update([
                'unlinked_at' => now(),
                'updated_at' => now(),
            ]);
    }
}

