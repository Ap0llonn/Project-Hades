<?php

namespace App\Features\Auth\Login\Identify;

final readonly class IdentifyCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
