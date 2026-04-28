<?php

namespace App\Features\Auth\MFA\TOTP\Generate;

use App\Models\MfaMethods;
use Ecotone\Modelling\Attribute\CommandHandler;

final class GenerateTotpHandler
{
    #[CommandHandler]
    public function handle(GenerateTotpCommand $command): void
    {
        MfaMethods::updateOrCreate(
            ['user_id' => $command->userId],
            [
                'totp_enabled' => false,
                'totp_secret' => $command->secret,
            ]
        );
    }
}
