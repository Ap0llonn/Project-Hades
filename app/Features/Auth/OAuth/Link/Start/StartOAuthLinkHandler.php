<?php

namespace App\Features\Auth\OAuth\Link\Start;

use App\Features\Auth\OAuth\Support\SupportedOAuthProviders;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

final class StartOAuthLinkHandler
{
    #[CommandHandler]
    public function handle(StartOAuthLinkCommand $command): StartOAuthLinkResult
    {
        $provider = SupportedOAuthProviders::normalize($command->provider);
        if (!SupportedOAuthProviders::isSupported($provider)) {
            throw ValidationException::withMessages([
                'provider' => 'Unsupported OAuth provider.',
            ]);
        }

        $driver = Socialite::driver($provider)->redirectUrl($command->callbackUrl);

        if ($provider === 'google') {
            $driver = $driver->scopes(['openid', 'email', 'profile']);
        }

        if ($provider === 'apple') {
            $driver = $driver
                ->scopes(['name', 'email'])
                ->with(['response_mode' => 'query']);
        }

        return new StartOAuthLinkResult($driver->redirect()->getTargetUrl());
    }
}
