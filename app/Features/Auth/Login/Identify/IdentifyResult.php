<?php

namespace App\Features\Auth\Login\Identify;

final readonly class IdentifyResult
{
    public function __construct(
        public string $userId,
        public bool $mfaActivated,
    ) {
    }
}
