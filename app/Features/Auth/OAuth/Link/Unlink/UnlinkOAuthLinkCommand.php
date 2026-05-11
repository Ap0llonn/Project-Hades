<?php

namespace App\Features\Auth\OAuth\Link\Unlink;

final readonly class UnlinkOAuthLinkCommand
{
    public function __construct(
        public string $userId,
        public string $provider,
    ) {
    }
}

