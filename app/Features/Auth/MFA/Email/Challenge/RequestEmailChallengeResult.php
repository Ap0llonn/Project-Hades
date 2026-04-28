<?php

namespace App\Features\Auth\MFA\Email\Challenge;

final readonly class RequestEmailChallengeResult
{
    public function __construct(
        public array $verificationState,
        public bool $delivered,
    ) {
    }
}

