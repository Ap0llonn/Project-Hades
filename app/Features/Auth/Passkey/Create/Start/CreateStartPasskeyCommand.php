<?php

namespace App\Features\Auth\Passkey\Create\Start;

final readonly class CreateStartPasskeyCommand
{
    public function __construct(
        public string $userId,
        public string $hostName,
        public string $appUrl,
    ) {
    }
}
