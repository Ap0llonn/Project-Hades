<?php

namespace App\Features\Dashboard\Service\Delete;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DeleteServiceController
{
    public function __invoke(Request $request, CommandBus $commandBus, string $serviceId): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $deleted = $commandBus->send(new DeleteServiceCommand(
            userId: (string) $user->id,
            serviceId: $serviceId,
        ));

        if (!$deleted) {
            return response()->json([
                'message' => 'Service not found.',
            ], 404);
        }

        return response()->json([], 204);
    }
}

