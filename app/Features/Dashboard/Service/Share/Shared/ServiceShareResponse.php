<?php

namespace App\Features\Dashboard\Service\Share\Shared;

use App\Models\ServiceShare;

final class ServiceShareResponse
{
    /**
     * @return array<string, mixed>
     */
    public static function fromModel(ServiceShare $share): array
    {
        $service = $share->service;

        return [
            'id' => (string) $share->id,
            'service_id' => (string) $share->service_id,
            'owner_user_id' => (string) $share->owner_user_id,
            'recipient_user_id' => (string) $share->recipient_user_id,
            'key_envelope' => is_array($share->key_envelope) ? $share->key_envelope : [],
            'created_at' => optional($share->created_at)?->toISOString(),
            'updated_at' => optional($share->updated_at)?->toISOString(),
            'shared_by' => $share->owner === null ? null : [
                'id' => (string) $share->owner->id,
                'email' => (string) $share->owner->email,
                'public_key' => (string) $share->owner->public_key,
            ],
            'service' => $service === null ? null : [
                'id' => (string) $service->id,
                'type' => (string) $service->type,
                'favorite' => (bool) $service->favorite,
                'status' => (string) $service->status,
                'payload' => is_array($service->data) ? $service->data : [],
                'created_at' => optional($service->created_at)?->toISOString(),
                'updated_at' => optional($service->updated_at)?->toISOString(),
            ],
        ];
    }
}
