<?php

namespace App\Features\Auth\MFA\TOTP\VerifyChallenge;

final readonly class VerifyTotpChallengeCommand
{
    public function __construct(
        public string $userId,
        public string $code,
        public string $method,
    ) {
    }
}
