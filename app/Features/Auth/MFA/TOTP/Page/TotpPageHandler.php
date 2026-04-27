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

        return [
            'emailHint' => $this->maskEmail((string) $user->email),
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
