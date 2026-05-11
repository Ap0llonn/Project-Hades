<?php

namespace App\Features\Auth\Dek;

final readonly class FetchDekBootstrapResult
{
    public function toArray(): array
    {
        return [
            'auth_method' => [
                'primary' => $this->primaryAuthMethod,
                'mfa' => $this->mfaAuthMethod,
            ],
            'wrapped_private_key' => $this->wrappedPrivateKey['wrapped_private_key'] ?? $this->wrappedPrivateKey,
            'dek_wrapper' => $this->dekWrapper,
        ];
    }

    public function __construct(
        public string $primaryAuthMethod,
        public string $mfaAuthMethod,
        public array $wrappedPrivateKey,
        public array $dekWrapper,
    ) {
    }
}

