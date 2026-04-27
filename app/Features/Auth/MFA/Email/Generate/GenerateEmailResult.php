<?php

namespace App\Features\Auth\MFA\Email\Generate;

final readonly class GenerateEmailResult
{
    public function __construct(
        public array $verificationState,
        public bool $delivered,
    ) {
    }
}

