<?php

namespace App\Features\Extension\Service\Create;

use App\Features\Dashboard\Service\Create\CreateServiceCommand;
use App\Features\Dashboard\Service\Shared\ServiceResponse;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class CreateExtensionServiceController
{
    public function __invoke(CreateExtensionServiceRequest $request, CommandBus $commandBus): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validated();
        $service = $commandBus->send(new CreateServiceCommand(
            userId: (string) $user->id,
            type: 'login',
            favorite: false,
            status: 'active',
            payload: [
                'schema' => 'extension.plain_login',
                'encoding' => 'plain',
                'name' => (string) ($validated['name'] ?? ''),
                'type' => 'login',
                'username' => (string) ($validated['username'] ?? ''),
                'password' => (string) ($validated['password'] ?? ''),
                'url' => (string) ($validated['url'] ?? ''),
                'note' => '',
                'requireMasterPassword' => false,
                'createdAt' => now()->toIso8601String(),
            ],
        ));

        return response()->json([
            'data' => ServiceResponse::fromModel($service),
            'status' => 'Service saved to vault.',
        ], 201);
    }
}

