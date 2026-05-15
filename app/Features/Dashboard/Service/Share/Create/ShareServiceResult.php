<?php

namespace App\Features\Dashboard\Service\Share\Create;

use App\Models\ServiceShare;

final readonly class ShareServiceResult
{
    private function __construct(
        public string $status,
        public ?ServiceShare $share = null,
    ) {
    }

    public static function serviceNotFound(): self
    {
        return new self('service_not_found');
    }

    public static function created(ServiceShare $share): self
    {
        return new self('created', $share);
    }

    public static function updated(ServiceShare $share): self
    {
        return new self('updated', $share);
    }
}
