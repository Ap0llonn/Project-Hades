<?php

namespace App\Features\Dashboard\Service\Update;

final readonly class UpdateServiceCommand
{
    /**
     * @param array<string, mixed> $changes
     */
    public function __construct(
        public string $userId,
        public string $serviceId,
        public array $changes,
    ) {
    }
}

