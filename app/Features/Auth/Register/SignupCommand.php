<?php

namespace App\Features\Auth\Register;

final readonly class SignupCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
