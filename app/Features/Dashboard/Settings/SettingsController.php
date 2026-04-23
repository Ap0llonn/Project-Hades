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
        $userMfa = $user->mfa()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'totp_enabled' => false,
                'totp_secret' => null,
                'recovery_codes' => [],
                'recovery_codes_show' => false,
                'mfa_activated' => false,
            ],
        );

        return Inertia::render('dashboard/settings/pages/SettingsPage', [
            'security' => [
                'mfa_activated' => (bool) ($userMfa?->mfa_activated ?? false),
                'totp_enabled' => (bool) ($userMfa?->totp_enabled ?? false),
            ]
        ]);
    }
}
