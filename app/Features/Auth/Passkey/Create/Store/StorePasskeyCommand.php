<?php

namespace App\Features\Auth\Passkey\Create\Store;

final readonly class StorePasskeyCommand
{
    /**
     * @param array<string, mixed> $wrappedDek
     */
    public function __construct(
        public string $userId,
        public string $passkeyJson,
        public string $passkeyOptionsJson,
        public string $hostName,
        public string $appUrl,
        public ?string $name,
        public array $wrappedDek,
    ) {
    }
}
