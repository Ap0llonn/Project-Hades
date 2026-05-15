<?php

namespace App\Features\Dashboard\Service\Share\Read;

final readonly class ListIncomingSharesQuery
{
    public function __construct(
        public string $recipientUserId,
        public ?string $type = null,
        public ?string $status = null,
        public ?string $search = null,
    ) {
    }
}
