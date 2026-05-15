<?php

namespace App\Features\Dashboard\Settings\Sessions\Revoke;

final readonly class RevokeSessionCommand
{
    public function __construct(
        public string $userId,
        public string $sessionId,
        public string $channel,
        public string $currentSessionId,
    ) {
    }
}
