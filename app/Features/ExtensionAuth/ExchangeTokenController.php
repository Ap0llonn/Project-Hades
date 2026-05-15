<?php

namespace App\Features\ExtensionAuth;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use App\Features\ExtensionAuth\Shared\InvalidExtensionCredentialException;
use Illuminate\Http\JsonResponse;

final class ExchangeTokenController
{
    public function __invoke(ExchangeTokenRequest $request, ExtensionTokenService $tokenService): JsonResponse
    {
        $payload = $request->validated();
        $code = (string) ($payload['code'] ?? '');

        try {
            $tokens = $tokenService->exchangeAuthorizationCode($code, $request);
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

