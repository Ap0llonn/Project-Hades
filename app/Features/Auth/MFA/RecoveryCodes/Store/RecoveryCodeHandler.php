<?php

namespace App\Features\Auth\MFA\RecoveryCodes\Store;

use App\Models\MfaMethods;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Auth;

class RecoveryCodeHandler
{

    #[CommandHandler]
    public function handle(RecoveryCodeCommand $command): void
    {
        $user = Auth::user();
        MfaMethods::where('user_id', $user->id)->update([
            'recovery_codes' => $command->recoveryCodes
        ]);

    }
}
