<?php

namespace App\Features\Auth\OAuth\List;

final readonly class FetchOAuthLinksQuery
{
    public function __construct(
        public string $userId,
    ) {
    }
}

