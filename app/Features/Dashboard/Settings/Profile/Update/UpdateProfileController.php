<?php

namespace App\Features\Dashboard\Settings\Profile\Update;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class UpdateProfileController
{
    public function __invoke(UpdateProfileRequest $request, CommandBus $commandBus): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validated();

        $firstName = isset($validated['first_name']) ? trim((string) $validated['first_name']) : '';
        $lastName = isset($validated['last_name']) ? trim((string) $validated['last_name']) : '';

        $updated = $commandBus->send(new UpdateProfileCommand(
            userId: (string) $user->id,
            firstName: $firstName !== '' ? $firstName : null,
            lastName: $lastName !== '' ? $lastName : null,
        ));

        if ($updated === null) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => (string) $updated->id,
                'email' => (string) $updated->email,
                'first_name' => (string) ($updated->first_name ?? ''),
                'last_name' => (string) ($updated->last_name ?? ''),
            ],
        ]);
    }
}
