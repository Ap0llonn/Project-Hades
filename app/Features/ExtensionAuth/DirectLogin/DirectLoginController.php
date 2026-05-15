<?php

namespace App\Features\ExtensionAuth\DirectLogin;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use App\Models\User;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class DirectLoginController
{
    public function __invoke(
        DirectLoginRequest $request,
        CommandBus $commandBus,
        ExtensionTokenService $tokenService,
    ): JsonResponse {
        $result = $commandBus->send(new DirectLoginCommand(
            (string) $request->input('email'),
            (string) $request->input('password'),
        ));

        /** @var User $user */
        $user = User::query()->findOrFail($result->userId);
        $tokens = $tokenService->issueTokenPair($user, $request);

        return response()->json(['data' => $tokens]);
    }
}
