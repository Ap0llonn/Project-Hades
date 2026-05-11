<?php

namespace App\Features\Auth\Passkey\Login\GenerateOptions;

final readonly class GeneratePasskeyAuthenticationOptionsResult
{
    public function __construct(
        public string $optionsJson,
    ) {
    }
}
