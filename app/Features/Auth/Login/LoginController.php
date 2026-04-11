<?php

namespace App\Features\Auth\Login;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class LoginController
{
    public function __invoke(LoginRequest $request, CommandBus $commandBus): JsonResponse
    {
        $result = $commandBus->send(
            new LoginCommand(
                email: (string) $request->string('email'),
                password: (string) $request->string('password'),
                remember: $request->boolean('remember'),
            )
        );

        

        return response()->json($result);
    }
}
