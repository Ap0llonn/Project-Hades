<?php

namespace App\Features\Auth\OAuth\Support;

final class SupportedOAuthProviders
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return ['google', 'apple'];
    }

    public static function isSupported(string $provider): bool
    {
        return in_array(self::normalize($provider), self::all(), true);
    }

    public static function normalize(string $provider): string
    {
        return strtolower(trim($provider));
    }
}

