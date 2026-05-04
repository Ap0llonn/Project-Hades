<?php

namespace App\Features\Auth\Dek;

final readonly class FetchDekBootstrapQuery
{

    public function __construct(
        public string $userId,
        public array $preferredWrapperTypes,
        public string $primaryAuthMethod,
        public string $mfaAuthMethod,
        public array $privateKeyWrapper,
    ) {
    }
}

