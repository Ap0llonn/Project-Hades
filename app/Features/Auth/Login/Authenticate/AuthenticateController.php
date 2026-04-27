<?php

namespace App\Features\Auth\Login\Authenticate;

use Auth;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class AuthenticateController
{
    public function __invoke(AuthenticateRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');
        $pendingMfaVerified = (bool) $request->session()->get('auth.pending_mfa_verified', false);

        if ($pendingUserId === '' || !$pendingMfaVerified) {
            $this->clearPendingState($request);
            return redirect()->route('login');
        }

        $authenticateResult = $commandBus->send(new AuthenticateCommand($pendingUserId));
        if (!$authenticateResult->authenticated) {
            $this->clearPendingState($request);
            return redirect()->route('login');
        }

        Auth::Login($authenticateResult->user);
        $request->session()->regenerate();
        $this->clearPendingState($request);

        return redirect()->intended(route('dashboard'));
    }

    private function clearPendingState(AuthenticateRequest $request): void
    {
        $request->session()->forget([
            'auth.pending_user_id',
            'auth.pending_started_at',
            'auth.pending_mfa_verified',
        ]);
    }
}
