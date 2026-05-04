<?php

namespace App\Features\Auth\OAuth\List;

final readonly class FetchOAuthLinksResult
{
    /**
     * @param list<array{name:string,key:string,linked:bool,account:string|null,linked_at:string|null,requires_passkey_setup:bool}> $providers
     */
    public function __construct(
        public array $providers,
    ) {
    }
}

