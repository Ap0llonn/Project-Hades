<?php

namespace App\Features\Auth\OAuth\Link\Start;

final readonly class StartOAuthLinkCommand
{
    public function __construct(
        public string $provider,
        public string $callbackUrl,
    ) {
    }
}

