<?php

namespace App\Features\Dashboard\Service\Share\Revoke;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RevokeServiceShareController
{
    public function __invoke(
        Request $request,
        CommandBus $commandBus,
        string $serviceId,
        string $shareId,
    ): JsonResponse {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $revoked = $commandBus->send(new RevokeServiceShareCommand(
            actorUserId: (string) $user->id,
            serviceId: $serviceId,
            shareId: $shareId,
        ));

        if (!$revoked) {
            return response()->json([
                'message' => 'Share not found.',
            ], 404);
        }

        return response()->json([], 204);
    }
}
