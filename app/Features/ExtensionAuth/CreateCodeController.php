<?php

namespace App\Features\ExtensionAuth;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CreateCodeController
{
    public function __invoke(Request $request, ExtensionTokenService $tokenService): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $result = $tokenService->issueAuthorizationCode($user, $request);

        return response()->json([
            'data' => $result,
        ], 201);
    }
}

