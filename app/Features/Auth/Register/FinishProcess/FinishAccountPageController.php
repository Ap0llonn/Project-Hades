<?php

namespace App\Features\Auth\Register\FinishProcess;

use App\Models\PendingUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class FinishAccountPageController
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $verifiedPendingUserId = (string) $request->session()->get('finish_account.pending_user_id', '');
        $requestedPendingUserId = (string) $request->query('id', '');

        if ($verifiedPendingUserId === '' || $verifiedPendingUserId !== $requestedPendingUserId) {
            return redirect()->route('start-account');
        }

        $pendingUser = PendingUser::find($verifiedPendingUserId);

        if (! $pendingUser || now()->gt($pendingUser->expires_at) || $pendingUser->used_at === null) {
            $request->session()->forget('finish_account.pending_user_id');
            return redirect()->route('start-account');
        }

        return Inertia::render('auth/pages/FinishAccountPage', [
            'email' => $pendingUser->email,
        ]);
    }
}
