<?php

namespace App\Features\Auth\Register;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public array $master_key_wrapper,
        public string $kdf_salt,
        public array $kdf_params
    ) {
    }
}
