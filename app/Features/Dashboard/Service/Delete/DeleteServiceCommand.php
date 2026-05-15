<?php

namespace App\Features\Dashboard\Service\Delete;

final readonly class DeleteServiceCommand
{
    public function __construct(
        public string $userId,
        public string $serviceId,
    ) {
    }
}

