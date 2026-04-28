<?php

namespace App\Features\Dashboard;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __invoke() : Response
    {
        return Inertia::render('dashboard/pages/DashboardPage');
    }
}
