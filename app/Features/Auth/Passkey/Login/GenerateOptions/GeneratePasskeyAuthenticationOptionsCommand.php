<?php

namespace App\Features\Auth\Passkey\Login\GenerateOptions;

final readonly class GeneratePasskeyAuthenticationOptionsCommand
{
    public function __construct(
        public string $hostName,
        public string $appUrl,
    )
    {
    }
}
