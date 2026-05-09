<?php

namespace App\Features\Auth\OAuth\Login\Start;

final readonly class StartOAuthLoginCommand
{
    public function __construct(
        public string $provider,
        public string $callbackUrl,
    ) {
    }
}

