<?php

namespace App\Features\Auth\Login;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

final class LoginController
{
    public function __invoke(LoginRequest $request, CommandBus $commandBus)
    {
        Log::info($request);

        return redirect()->route(route("home"));
    }
}
