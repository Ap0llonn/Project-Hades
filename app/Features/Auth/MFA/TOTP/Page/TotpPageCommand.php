<?php

namespace App\Features\Auth\MFA\TOTP\Page;

final readonly class TotpPageCommand
{
    public function __construct(
        public ?string $redirectTo,
    ) {
    }
}
