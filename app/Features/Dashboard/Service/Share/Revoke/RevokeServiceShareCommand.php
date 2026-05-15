<?php

namespace App\Features\Dashboard\Service\Share\Revoke;

final readonly class RevokeServiceShareCommand
{
    public function __construct(
        public string $actorUserId,
        public string $serviceId,
        public string $shareId,
    ) {
    }
}
