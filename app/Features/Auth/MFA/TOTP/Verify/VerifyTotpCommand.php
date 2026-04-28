<?php

namespace App\Features\Auth\MFA\TOTP\Verify;

final readonly class VerifyTotpCommand
{
    public function __construct(
        public string $userId,
        public string $code,
    ) {
    }
}
