<?php

namespace App\Features\Auth\OAuth\Login\Complete;

final readonly class CompleteOAuthLoginCommand
{
    public function __construct(
        public string $provider,
        public string $providerUserId,
    ) {
    }
}

