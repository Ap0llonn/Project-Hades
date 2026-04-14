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

        $pendingUser = PendingUser::find($request->id);

        if (! $pendingUser || now()->gt($pendingUser->expires_at)) {
            return redirect()->route('start-account');
        }

        return Inertia::render('auth/pages/FinishAccountPage', [
            'email' => $pendingUser->email,
        ]);
    }
}
