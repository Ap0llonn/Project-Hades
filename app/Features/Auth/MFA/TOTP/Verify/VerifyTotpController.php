<?php

namespace App\Features\Auth\MFA\TOTP\Verify;

use App\Models\MfaMethods;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class VerifyTotpController
{
    public function __invoke(VerifyTotpRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $payload = $request->validated();

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $commandBus->send(new VerifyTotpCommand(
            $user->id,
            $payload['code']
        ));

        $mfaMethods = MfaMethods::query()->where('user_id', $user->id)->first();
        if ($mfaMethods && !$mfaMethods->recovery_codes_show) {
            $mfaMethods->update([
                'recovery_codes_show' => true,
            ]);
            return redirect()->route('mfa.recovery-codes');
        }

        return redirect()->intended(route('dashboard'));
    }
}
