<?php

namespace App\Features\Auth\MFA\Email\Disable;

final readonly class DisableEmailCommand
{
    public function __construct(
        public string $userId,
        public string $masterPassword,
    ) {
    }
}

