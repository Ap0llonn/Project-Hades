<?php

namespace App\Features\Auth\Dek;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DekController
{
    public function __invoke(Request $request, FetchDekBootstrapHandler $handler): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401, $this->securityHeaders());
        }

        $authPrimaryMethod = (string) $request->session()->get('auth.primary_method', 'password');
        $authMfaMethod = (string) $request->session()->get('auth.mfa_method', '');
        $preferredWrapperTypes = $this->resolvePreferredWrapperTypes($authPrimaryMethod, $authMfaMethod);

        $view = $handler->handle(new FetchDekBootstrapQuery(
            userId: (string) $user->id,
            preferredWrapperTypes: $preferredWrapperTypes,
            primaryAuthMethod: $authPrimaryMethod,
            mfaAuthMethod: $authMfaMethod,
            privateKeyWrapper: is_array($user->private_key_wrapper) ? $user->private_key_wrapper : [],
        ));

        if (!$view->vaultFound) {
            return response()->json([
                'message' => 'Vault not found for user.',
            ], 404, $this->securityHeaders());
        }

        if ($view->result === null) {
            return response()->json([
                'message' => 'No active key wrapper found for the current authentication method.',
                'expected_wrapper_types' => $preferredWrapperTypes,
            ], 404, $this->securityHeaders());
        }

        return response()->json($view->result->toArray(), 200, $this->securityHeaders());
    }

    /**
     * @return list<string>
     */
    private function resolvePreferredWrapperTypes(string $primaryMethod, string $mfaMethod): array
    {
        if ($primaryMethod === 'passkey') {
            return ['passkey'];
        }

        if ($primaryMethod === 'oauth') {
            return ['oauth'];
        }

        if ($primaryMethod === 'password' && $mfaMethod === 'recovery') {
            return ['recovery', 'password'];
        }

        return ['password'];
    }

    /**
     * @return array<string, string>
     */
    private function securityHeaders(): array
    {
        return [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
    }
}
