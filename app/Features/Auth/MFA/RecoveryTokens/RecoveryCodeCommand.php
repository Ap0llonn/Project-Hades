<?php

namespace App\Features\Auth\MFA\RecoveryTokens;

final readonly class RecoveryCodeCommand
{
    public function __construct(
        public array $recoveryCodes
    ){}
}
