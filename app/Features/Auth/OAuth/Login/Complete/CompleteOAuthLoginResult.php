<?php

namespace App\Features\Auth\OAuth\Login\Complete;

final readonly class CompleteOAuthLoginResult
{
    public function __construct(
        public ?string $userId,
        public bool $mfaActivated,
    ) {
    }
}

