<?php

namespace App\Features\Auth\MFA\Email\Challenge;

final readonly class RequestEmailChallengeCommand
{
    public function __construct(
        public string $userId,
        public bool $force,
        public ?array $existingVerificationState,
    ) {
    }
}

