<?php

namespace App\Features\Auth\MFA\Email\Challenge;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class RequestEmailChallengeController
{
    public function __invoke(RequestEmailChallengeRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $payload = $request->validated();
        $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');
        if ($pendingUserId === '') {
            return redirect()->route('login');
        }

        $existingState = $request->session()->get('auth.pending_email_mfa');
        $result = $commandBus->send(new RequestEmailChallengeCommand(
            userId: $pendingUserId,
            force: (bool) ($payload['force'] ?? false),
            existingVerificationState: is_array($existingState) ? $existingState : null,
        ));

        $request->session()->put('auth.pending_email_mfa', $result->verificationState);

        return redirect()->route('mfa');
    }
}
