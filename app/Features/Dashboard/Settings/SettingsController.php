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


        return Inertia::render('dashboard/settings/pages/SettingsPage', [
            'security' => [
                'mfa_activated' =>  $user->mfa->mfa_activated ?? false,
                'totp_enabled' => $user->mfa->totp_enabled ?? false,
                'email_enabled' => $user->mfa->email_enabled ?? false,
            ]
        ]);
    }
}
