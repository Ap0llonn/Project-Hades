<?php

namespace App\Features\Auth\MFA\TOTP;

use App\Models\MfaMethods;
use Ecotone\Modelling\Attribute\CommandHandler;
use OTPHP\TOTP;
use Illuminate\Validation\ValidationException;

class TotpHandler
{


    #[CommandHandler]
    public function handle(TotpCommand $command) : void{

        $mfaMethods = MfaMethods::query()->where('user_id', $user->id)->first();
        if (!$mfaMethods || !$mfaMethods->totp_secret) {

            throw ValidationException::withMessages([
                'code' => 'MFA is not enabled.',
            ]);
        }

        $totp = TOTP::create($mfaMethods->totp_secret);
        $isValid = $totp->verify($payload['code']);

        if (!$isValid) {
            throw ValidationException::withMessages([
                'code' => 'Invalid code.',
            ]);
        }

        if (!$mfaMethods->totp_enabled){
            $mfaMethods->update([
                'totp_enabled' => true,
            ]);
        }


    }
}
