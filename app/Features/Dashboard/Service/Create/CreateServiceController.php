<?php

namespace App\Features\Dashboard\Service\Create;

use App\Features\Dashboard\Service\Shared\ServiceResponse;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class CreateServiceController
{
    public function __invoke(CreateServiceRequest $request, CommandBus $commandBus): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $payload = $request->validated();
        $service = $commandBus->send(new CreateServiceCommand(
            userId: (string) $user->id,
            type: (string) ($payload['type'] ?? ''),
            favorite: (bool) ($payload['favorite'] ?? false),
            status: (string) ($payload['status'] ?? 'active'),
            payload: is_array($payload['payload'] ?? null) ? $payload['payload'] : [],
        ));

        return response()->json([
            'data' => ServiceResponse::fromModel($service),
        ], 201);
    }
}
