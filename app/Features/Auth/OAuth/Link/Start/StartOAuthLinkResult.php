<?php

namespace App\Features\Auth\OAuth\Link\Start;

final readonly class StartOAuthLinkResult
{
    public function __construct(
        public string $redirectUrl,
    ) {
    }
}

