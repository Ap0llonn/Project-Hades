<?php

namespace App\Features\Auth\Passkey\Login\Authenticate;

final readonly class AuthenticateUsingPasskeyCommand
{
    public function __construct(
        public string $startAuthenticationResponseJson,
        public string $passkeyAuthenticationOptionsJson,
        public string $hostName,
        public string $appUrl,
    ) {
    }
}
