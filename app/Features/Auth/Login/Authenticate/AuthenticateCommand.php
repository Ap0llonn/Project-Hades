<?php

namespace App\Features\Auth\Login\Authenticate;

final readonly class AuthenticateCommand
{
    public function __construct(
        public string $userId
    ) {
    }
}
