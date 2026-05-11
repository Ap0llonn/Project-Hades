<?php

namespace App\Features\Auth\Passkey\Create\Start;

final readonly class CreateStartPasskeyResult
{
    public function __construct(
        public string $optionsJson,
    ) {
    }
}
