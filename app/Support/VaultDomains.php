<?php

namespace App\Support;

final class VaultDomains
{
    private const DEV_ENVS = ['local', 'development', 'testing'];

    public static function marketingHost(): string
    {
        return self::isDevMode() ? 'vaultguardian.test' : 'vaultguardian.ca';
    }

    public static function vaultHost(): string
    {
        return self::isDevMode() ? 'vault.vaultguardian.test' : 'vault.vaultguardian.ca';
    }

    private static function isDevMode(): bool
    {
        return app()->environment(self::DEV_ENVS);
    }
}
