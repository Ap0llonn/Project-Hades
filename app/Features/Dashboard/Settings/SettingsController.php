<?php

namespace App\Features\Dashboard\Settings;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController
{
    public function __invoke() : Response
    {
        $user = Auth::user();
        $userMfa = $user->mfa;
        return Inertia::render('dashboard/settings/pages/SettingsPage', [
            'security' => [
                'mfa_activated' => $userMfa->mfa_activated,
                'totp_enabled' => $userMfa->totp_enabled,
            ]
        ]);
    }
}
