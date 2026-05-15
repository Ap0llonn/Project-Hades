<?php

namespace App\Features\Dashboard\Service\Shared;

use App\Models\VaultService;

final class ServiceResponse
{
    /**
     * @return array<string, mixed>
     */
    public static function fromModel(VaultService $service): array
    {
        return [
            'id' => (string) $service->id,
            'user_id' => (string) $service->user_id,
            'type' => (string) $service->type,
            'favorite' => (bool) $service->favorite,
            'status' => (string) $service->status,
            'payload' => is_array($service->data) ? $service->data : [],
            'created_at' => optional($service->created_at)?->toISOString(),
            'updated_at' => optional($service->updated_at)?->toISOString(),
        ];
    }
}
