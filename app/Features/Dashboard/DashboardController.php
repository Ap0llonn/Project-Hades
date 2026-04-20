<?php

namespace App\Features\Dashboard;

use Inertia\Inertia;

class DashboardController
{
    public function __invoke()
    {
        return Inertia::render('dashboard/pages/DashboardPage');
    }
}
