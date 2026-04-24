<?php

namespace App\Features\Auth\MFA\TOTP\Generate;

final readonly class GenerateTotpCommand
{
    public function __construct(
        public int $userId,
        public string $secret,
    ) {
    }
}
