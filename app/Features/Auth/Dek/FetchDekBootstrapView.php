<?php

namespace App\Features\Auth\Dek;

final readonly class FetchDekBootstrapView
{
    public function __construct(
        public bool $vaultFound,
        public ?FetchDekBootstrapResult $result,
    ) {
    }
}

