<?php

namespace App\Features\Auth\MFA\TOTP;

class TotpCommand
{
    public function __construct(
        public string $code
    )
    {

    }
}
