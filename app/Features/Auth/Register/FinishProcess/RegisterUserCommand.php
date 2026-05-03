<?php

namespace App\Features\Auth\Register\FinishProcess;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public array $wrapped_private_key,
        public array $wrapped_dek,
        public string $public_key
    ) {
    }
}
