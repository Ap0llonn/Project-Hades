<?php

namespace App\Features\Dashboard\Service\Read;

final readonly class ListServicesQuery
{
    public function __construct(
        public string $userId,
        public ?string $type = null,
        public ?string $status = null,
        public ?string $search = null,
    ) {
    }
}

