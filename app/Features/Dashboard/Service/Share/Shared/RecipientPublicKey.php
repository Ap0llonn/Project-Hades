<?php

namespace App\Features\Dashboard\Service\Share\Shared;

final readonly class RecipientPublicKey
{
    public function __construct(
        public string $userId,
        public string $email,
        public string $publicKey,
    ) {
    }
}
