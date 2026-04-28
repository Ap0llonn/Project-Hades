<?php

namespace App\Features\Auth\MFA\Email\Verify;

use App\Models\MfaMethods;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

class EmailVerifyController
{
    public function __invoke(EmailVerifyRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $payload = $request->validated();
        $verificationState = $request->session()->get('mfa.email_verification');

        $commandBus->send(new EmailVerifyCommand(
            userId: (string) $user->id,
            code: (string) $payload['code'],
            verificationState: is_array($verificationState) ? $verificationState : null,
        ));

        $request->session()->forget('mfa.email_verification');

        $mfaMethods = MfaMethods::query()->where('user_id', $user->id)->first();
        if ($mfaMethods && !$mfaMethods->recovery_codes_show) {
            $mfaMethods->update([
                'recovery_codes_show' => true,
            ]);

            return redirect()->route('mfa.recovery-codes');
        }

        return redirect()->route('settings');
    }
}
