<?php

namespace App\Features\Auth\MFA\RecoveryCodes\Store;

final readonly class RecoveryCodeCommand
{
    public function __construct(
        public array $recoveryCodes
    ){}
}
