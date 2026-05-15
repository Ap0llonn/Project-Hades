<?php

namespace App\Features\Dashboard\Settings\Sessions\Revoke;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class RevokeSessionController
{
    public function __invoke(
        RevokeSessionRequest $request,
        CommandBus $commandBus,
        string $sessionId,
    ): JsonResponse {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validated();
        $result = $commandBus->send(new RevokeSessionCommand(
            userId: (string) $user->id,
            sessionId: $sessionId,
            channel: (string) ($validated['channel'] ?? ''),
            currentSessionId: (string) $request->session()->getId(),
        ));

        return match ($result->status) {
            'revoked' => response()->json([
                'status' => 'revoked',
            ], 200),
            'current_session' => response()->json([
                'message' => 'Cannot revoke the current session.',
            ], 422),
            'invalid_channel' => response()->json([
                'message' => 'Invalid session channel.',
            ], 422),
            default => response()->json([
                'message' => 'Session not found.',
            ], 404),
        };
    }
}
