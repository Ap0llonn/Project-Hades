<?php

namespace App\Features\Dashboard\Service\Shared;

final class ServiceType
{
    public const LOGIN = 'login';
    public const CREDIT_CARD = 'credit_card';
    public const NOTE = 'note';
    public const IDENTITY = 'identity';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::LOGIN,
            self::CREDIT_CARD,
            self::NOTE,
            self::IDENTITY,
        ];
    }

    public static function normalize(string $value): ?string
    {
        $normalized = strtolower(trim($value));
        if ($normalized === 'card') {
            return self::CREDIT_CARD;
        }

        return in_array($normalized, self::values(), true) ? $normalized : null;
    }
}

