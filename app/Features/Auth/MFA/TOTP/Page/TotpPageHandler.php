<?php

namespace App\Features\Auth\MFA\TOTP\Page;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;

final class TotpPageHandler
{
    #[CommandHandler]
    public function handle(TotpPageCommand $command): array
    {
        $user = User::query()->whereKey($command->pendingUserId)->first();
        if (!$user) {
            return [];
        }

        $mfa = $user->mfa;
        $methods = [
            'totp' => (bool) ($mfa?->totp_enabled && $mfa?->totp_secret),
            'email' => (bool) $mfa?->email_enabled,
            'recovery' => is_array($mfa?->recovery_codes) && count($mfa->recovery_codes) > 0,
        ];

        if (!$methods['totp'] && !$methods['email'] && !$methods['recovery']) {
            $methods['totp'] = true;
        }

        return [
            'emailHint' => $this->maskEmail((string) $user->email),
            'methods' => $methods,
        ];
    }

    private function maskEmail(string $email): string
    {
        $atPosition = strpos($email, '@');
        if ($atPosition === false) {
            return '';
        }

        $local = substr($email, 0, $atPosition);
        $domain = substr($email, $atPosition + 1);

        if ($local === '') {
            return '***@' . $domain;
        }

        $visible = $local[0];
        return $visible . '***@' . $domain;
    }
}
