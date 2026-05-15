<?php

namespace App\Features\Dashboard\Service\Read;

use App\Features\Dashboard\Service\Shared\ServiceResponse;
use Illuminate\Http\JsonResponse;

final class ReadServiceController
{
    public function __invoke(ReadServiceRequest $request, ReadServiceHandler $handler): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validated();
        $serviceId = (string) ($validated['service_id'] ?? '');

        if ($serviceId !== '') {
            $service = $handler->find(new FindServiceQuery(
                userId: (string) $user->id,
                serviceId: $serviceId,
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

        $services = $handler->list(new ListServicesQuery(
            userId: (string) $user->id,
            type: isset($validated['type']) ? (string) $validated['type'] : null,
            status: isset($validated['status']) ? (string) $validated['status'] : null,
            search: isset($validated['search']) ? (string) $validated['search'] : null,
        ));

        return response()->json([
            'data' => $services->map(static fn ($service): array => ServiceResponse::fromModel($service))->values(),
            'meta' => [
                'count' => $services->count(),
            ],
        ]);
    }
}

