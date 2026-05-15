<?php

namespace App\Features\Dashboard\Settings\Sessions\Revoke;

final readonly class RevokeSessionResult
{
    private function __construct(
        public string $status,
    ) {
    }

    public static function revoked(): self
    {
        return new self('revoked');
    }

    public static function notFound(): self
    {
        return new self('not_found');
    }

    public static function invalidChannel(): self
    {
        return new self('invalid_channel');
    }

    public static function currentSession(): self
    {
        return new self('current_session');
    }
}
