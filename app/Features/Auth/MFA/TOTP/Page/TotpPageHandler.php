<?php

namespace App\Features\Auth\MFA\TOTP\Page;

use Ecotone\Modelling\Attribute\CommandHandler;

final class TotpPageHandler
{
    #[CommandHandler]
    public function handle(TotpPageCommand $command): void
    {
        if ($command->redirectTo !== null && !str_starts_with($command->redirectTo, '/')) {
            return;
        }
    }
}
