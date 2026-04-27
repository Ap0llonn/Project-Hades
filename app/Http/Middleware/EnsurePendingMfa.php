<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePendingMfa
{
    private const PENDING_TTL_SECONDS = 600;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }

        $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');
        $pendingStartedAt = (int) $request->session()->get('auth.pending_started_at', 0);

        if ($pendingUserId === '' || $pendingStartedAt === 0) {
            $this->clearPendingState($request);
            return redirect()->route('login');
        }

        if ((time() - $pendingStartedAt) > self::PENDING_TTL_SECONDS) {
            $this->clearPendingState($request);
            return redirect()->route('login');
        }

        if (!User::query()->whereKey($pendingUserId)->exists()) {
            $this->clearPendingState($request);
            return redirect()->route('login');
        }

        return $next($request);
    }

    private function clearPendingState(Request $request): void
    {
        $request->session()->forget([
            'auth.pending_user_id',
            'auth.pending_started_at',
            'auth.pending_mfa_verified',
        ]);
    }
}
