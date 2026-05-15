<?php

namespace App\Features\ExtensionAuth\DirectLogin;

final readonly class DirectLoginResult
{
    public function __construct(
        public string $userId,
    ) {}
}
