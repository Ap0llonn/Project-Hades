<?php

namespace App\Features\Auth\OAuth\Link\Complete;

final readonly class CompleteOAuthLinkCommand
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public string $userId,
        public string $provider,
        public string $providerUserId,
        public ?string $providerEmail,
        public ?string $providerName,
        public ?string $providerAvatar,
        public ?string $token,
        public ?string $refreshToken,
        public ?int $expiresInSeconds,
        public array $metadata,
    ) {
    }
}

