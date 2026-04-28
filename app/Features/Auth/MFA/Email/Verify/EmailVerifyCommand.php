<?php

namespace App\Features\Auth\MFA\Email\Verify;

final readonly class EmailVerifyCommand
{
    public function __construct(
        public string $userId,
        public string $code,
        public ?array $verificationState,
    ) {
    }
}

