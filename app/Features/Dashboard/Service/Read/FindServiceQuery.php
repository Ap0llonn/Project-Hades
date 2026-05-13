<?php

namespace App\Features\Dashboard\Service\Read;

final readonly class FindServiceQuery
{
    public function __construct(
        public string $userId,
        public string $serviceId,
    ) {
    }
}

