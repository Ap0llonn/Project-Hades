<?php

namespace App\Features\ExtensionAuth\DirectLogin;

final readonly class DirectLoginCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
