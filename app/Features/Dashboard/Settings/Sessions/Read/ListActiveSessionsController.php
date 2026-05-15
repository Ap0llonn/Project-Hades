<?php

namespace App\Features\Dashboard\Settings\Sessions\Read;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListActiveSessionsController
{
    public function __invoke(Request $request, ListActiveSessionsHandler $handler): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $sessions = $handler->handle(new ListActiveSessionsQuery(
            userId: (string) $user->id,
            currentSessionId: (string) $request->session()->getId(),
        ));

        return response()->json([
            'data' => $sessions,
            'meta' => [
                'count' => count($sessions),
            ],
        ]);
    }
}
