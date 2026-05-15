<?php

namespace App\Features\Dashboard\Service\Share\Create;

use App\Features\Dashboard\Service\Share\Shared\RecipientPublicKeyDirectory;
use App\Features\Dashboard\Service\Share\Shared\ServiceShareResponse;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class ShareServiceController
{
    public function __invoke(
        ShareServiceRequest $request,
        RecipientPublicKeyDirectory $directory,
        CommandBus $commandBus,
        string $serviceId,
    ): JsonResponse {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $payload = $request->validated();
        $recipient = $directory->findByEmail((string) ($payload['recipient_email'] ?? ''));
        if ($recipient === null) {
            return response()->json([
                'message' => 'Recipient not found.',
            ], 404);
        }

        if ((string) $recipient->userId === (string) $user->id) {
            return response()->json([
                'message' => 'Cannot share a service with yourself.',
            ], 422);
        }

        $result = $commandBus->send(new ShareServiceCommand(
            ownerUserId: (string) $user->id,
            recipientUserId: (string) $recipient->userId,
            serviceId: $serviceId,
            keyEnvelope: is_array($payload['key_envelope'] ?? null) ? $payload['key_envelope'] : [],
        ));

        if ($result->status === 'service_not_found' || $result->share === null) {
            return response()->json([
                'message' => 'Service not found.',
            ], 404);
        }

        $share = $result->share->loadMissing(['service', 'owner']);

        try {
            $serviceName = trim((string) ($share->service?->name ?? 'Shared vault item'));
            Mail::to($recipient->email)->send(new PasswordShareNotificationEmail(
                ownerEmail: (string) ($share->owner?->email ?? $user->email ?? 'Vault user'),
                serviceName: $serviceName !== '' ? $serviceName : 'Shared vault item',
                dashboardUrl: route('dashboard'),
            ));
        } catch (Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'data' => ServiceShareResponse::fromModel($share),
        ], $result->status === 'created' ? 201 : 200);
    }
}
