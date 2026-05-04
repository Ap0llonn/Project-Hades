<?php

namespace App\Features\Auth\Login\Authenticate;

use Auth;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

final class AuthenticateController
{
    public function __invoke(AuthenticateRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');
        $pendingMfaVerified = (bool) $request->session()->get('auth.pending_mfa_verified', false);
        $pendingPrimaryMethod = (string) $request->session()->get('auth.pending_primary_method', 'password');
        $pendingMfaMethod = (string) $request->session()->get('auth.pending_mfa_method', '');

        if ($pendingUserId === '' || !$pendingMfaVerified) {
            Log::info('error 1');
            $this->clearPendingState($request);
            return redirect()->route('login');
        }

        $authenticateResult = $commandBus->send(new AuthenticateCommand($pendingUserId));
        if (!$authenticateResult->user) {
            Log::info('error 2');
            $this->clearPendingState($request);
            return redirect()->route('login');
        }
        Log::info('user login');
        Auth::Login($authenticateResult->user);
        $request->session()->regenerate();
        $request->session()->put('auth.primary_method', $pendingPrimaryMethod);
        $request->session()->put('auth.mfa_method', $pendingMfaMethod);
        $this->clearPendingState($request);

        return redirect()->intended(route('dashboard'));
    }

    private function clearPendingState(AuthenticateRequest $request): void
    {
        $request->session()->forget([
            'auth.pending_user_id',
            'auth.pending_started_at',
            'auth.pending_mfa_verified',
            'auth.pending_email_mfa',
            'auth.pending_primary_method',
            'auth.pending_mfa_method',
        ]);
    }
}
