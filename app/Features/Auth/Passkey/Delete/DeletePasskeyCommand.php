<?php

namespace App\Features\Auth\Passkey\Delete;

final readonly class DeletePasskeyCommand
{
    public function __construct(
        public string $userId,
        public string $passkeyId,
    ) {
    }
}
