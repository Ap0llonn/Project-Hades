<?php

namespace App\Features\Auth\Register\StartProcess;

final readonly class StartAccountCommand
{
    public function __construct(
        public string $email,
    ) {
    }
}
