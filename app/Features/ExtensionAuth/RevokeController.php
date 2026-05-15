<?php

namespace App\Features\ExtensionAuth;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RevokeController
{
    public function __invoke(Request $request, ExtensionTokenService $tokenService): JsonResponse
    {
        $accessToken = $request->bearerToken();
        if (!is_string($accessToken) || trim($accessToken) === '') {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $tokenService->revokeByAccessToken($accessToken);

        return response()->json([
            'message' => 'Token revoked.',
        ]);
    }
}

