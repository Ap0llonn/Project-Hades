<?php

namespace App\Features\Auth\Passkey\Login\Authenticate;

use App\Models\User;

final readonly class AuthenticateUsingPasskeyResult
{
    public function __construct(
        public ?User $user,
    ) {
    }
}
