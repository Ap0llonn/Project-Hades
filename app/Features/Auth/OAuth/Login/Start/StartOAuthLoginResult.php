<?php

namespace App\Features\Auth\OAuth\Login\Start;

final readonly class StartOAuthLoginResult
{
    public function __construct(
        public string $redirectUrl,
    ) {
    }
}

