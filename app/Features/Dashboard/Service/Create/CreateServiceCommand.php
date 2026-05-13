<?php

namespace App\Features\Dashboard\Service\Create;

final readonly class CreateServiceCommand
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public string $userId,
        public string $type,
        public bool $favorite,
        public string $status,
        public array $payload,
    ) {
    }
}
