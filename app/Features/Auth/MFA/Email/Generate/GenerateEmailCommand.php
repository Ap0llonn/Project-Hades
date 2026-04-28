<?php

namespace App\Features\Auth\MFA\Email\Generate;

final readonly class GenerateEmailCommand
{
    public function __construct(
        public string $userId,
        public ?array $existingVerificationState,
    ) {
    }
}

