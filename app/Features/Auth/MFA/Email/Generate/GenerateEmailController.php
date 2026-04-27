<?php

namespace App\Features\Auth\MFA\Email\Generate;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

class GenerateEmailController
{
    public function __invoke(GenerateEmailRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $existingState = $request->session()->get('mfa.email_verification');
        $result = $commandBus->send(new GenerateEmailCommand(
            userId: (string) $user->id,
            existingVerificationState: is_array($existingState) ? $existingState : null,
        ));

        $request->session()->put('mfa.email_verification', $result->verificationState);

        return redirect()->route('settings');
    }
}
