<?php

namespace App\Features\Dashboard\Service\Share\Read;

use App\Features\Dashboard\Service\Share\Shared\ServiceShareResponse;
use Illuminate\Http\JsonResponse;

final class ListIncomingSharesController
{
    public function __invoke(ListIncomingSharesRequest $request, ListIncomingSharesHandler $handler): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validated();
        $shares = $handler->handle(new ListIncomingSharesQuery(
            recipientUserId: (string) $user->id,
            type: isset($validated['type']) ? (string) $validated['type'] : null,
            status: isset($validated['status']) ? (string) $validated['status'] : null,
            search: isset($validated['search']) ? (string) $validated['search'] : null,
        ));

        return response()->json([
            'data' => $shares->map(static fn ($share): array => ServiceShareResponse::fromModel($share))->values(),
            'meta' => [
                'count' => $shares->count(),
            ],
        ]);
    }
}
