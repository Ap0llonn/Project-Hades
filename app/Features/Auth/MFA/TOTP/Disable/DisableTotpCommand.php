<?php

namespace App\Features\Auth\MFA\TOTP\Disable;

final readonly class DisableTotpCommand
{
    public function __construct(
        public int $userId,
        public string $masterPassword,
    ) {
    }
}
