<?php

namespace App\Features\Auth\Register\StartProcess;

use Inertia\Inertia;
use Inertia\Response;

final class StartAccountPageController
{
    public function __invoke(): Response
    {
        return Inertia::render('auth/pages/StartAccountPage');
    }
}
