<?php

namespace App\Features\Auth\Login\Authenticate;

use App\Models\User;

final readonly class AuthenticateResult
{
    public function __construct(
        public User $user,
        public bool $authenticated,
    ) {
    }
}
