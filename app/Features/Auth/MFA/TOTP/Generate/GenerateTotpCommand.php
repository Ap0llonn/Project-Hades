<?php

namespace App\Features\Auth\MFA\TOTP\Generate;

final readonly class GenerateTotpCommand
{
    public function __construct(
        public string $userId,
        public string $secret,
    ) {
    }
}
