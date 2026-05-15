<?php

namespace App\Features\Dashboard\Settings\Sessions\Read;

final readonly class ListActiveSessionsQuery
{
    public function __construct(
        public string $userId,
        public string $currentSessionId,
    ) {
    }
}
