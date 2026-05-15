<?php

namespace App\Http\Middleware;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticateExtensionToken
{
    public function __construct(
        private readonly ExtensionTokenService $tokenService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->bearerToken();
        if (!is_string($rawToken) || trim($rawToken) === '') {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user = $this->tokenService->authenticateAccessToken($rawToken);
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(static fn () => $user);

        return $next($request);
    }
}

