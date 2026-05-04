<?php

namespace App\Features\Auth\OAuth\List;

use App\Models\OAuthAccount;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Schema;

final class FetchOAuthLinksHandler
{
    #[CommandHandler]
    public function handle(FetchOAuthLinksQuery $query): FetchOAuthLinksResult
    {
        $accounts = collect();
        if (Schema::hasTable('oauth_accounts')) {
            $accounts = OAuthAccount::query()
                ->where('user_id', $query->userId)
                ->whereNull('unlinked_at')
                ->get()
                ->keyBy('provider');
        }

        $providers = [];
        foreach (['google' => 'Google', 'apple' => 'Apple'] as $key => $label) {
            $account = $accounts->get($key);
            $providers[] = [
                'name' => $label,
                'key' => $key,
                'linked' => $account !== null,
                'account' => $account?->provider_email,
                'linked_at' => optional($account?->linked_at)->toIso8601String(),
                'requires_passkey_setup' => (bool) ($account?->metadata['requires_passkey_setup'] ?? false),
            ];
        }

        return new FetchOAuthLinksResult($providers);
    }
}
