<?php

namespace App\Features\Dashboard\Service\Share\Create;

final readonly class ShareServiceCommand
{
    /**
     * @param array<string, mixed> $keyEnvelope
     */
    public function __construct(
        public string $ownerUserId,
        public string $recipientUserId,
        public string $serviceId,
        public array $keyEnvelope,
    ) {
    }
}
