<?php

namespace App\Features\ExtensionAuth;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use App\Features\ExtensionAuth\Shared\InvalidExtensionCredentialException;
use Illuminate\Http\JsonResponse;

final class RefreshTokenController
{
    public function __invoke(RefreshTokenRequest $request, ExtensionTokenService $tokenService): JsonResponse
    {
        $payload = $request->validated();
        $refreshToken = (string) ($payload['refresh_token'] ?? '');

        try {
            $tokens = $tokenService->refreshTokenPair($refreshToken, $request);
        } catch (InvalidExtensionCredentialException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 401);
        }

        return response()->json([
            'data' => $tokens,
        ]);
    }
}

