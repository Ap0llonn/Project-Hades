<?php

namespace App\Features\Dashboard\Service\Update;

use App\Features\Dashboard\Service\Shared\ServiceResponse;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class UpdateServiceController
{
    public function __invoke(UpdateServiceRequest $request, CommandBus $commandBus, string $serviceId): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $changes = $request->validated();
        if (array_key_exists('payload', $changes)) {
            $changes['data'] = $changes['payload'];
            unset($changes['payload']);
        }

        $service = $commandBus->send(new UpdateServiceCommand(
            userId: (string) $user->id,
            serviceId: $serviceId,
            changes: $changes,
        ));

        if ($service === null) {
            return response()->json([
                'message' => 'Service not found.',
            ], 404);
        }

        return response()->json([
            'data' => ServiceResponse::fromModel($service),
        ]);
    }
}
