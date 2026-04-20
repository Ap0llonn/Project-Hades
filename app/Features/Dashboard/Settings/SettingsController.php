<?php

namespace App\Features\Dashboard\Settings;

use Inertia\Inertia;

class SettingsController
{
    public function __invoke()
    {
        return Inertia::render('dashboard/settings/pages/SettingsPage');
    }
}
