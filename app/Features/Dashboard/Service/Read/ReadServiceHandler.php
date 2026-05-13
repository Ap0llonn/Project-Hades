<?php

namespace App\Features\Dashboard\Service\Read;

use App\Models\VaultService;
use Illuminate\Database\Eloquent\Collection;

final class ReadServiceHandler
{
    public function find(FindServiceQuery $query): ?VaultService
    {
        return VaultService::query()
            ->where('id', $query->serviceId)
            ->where('user_id', $query->userId)
            ->first();
    }

    /**
     * @return Collection<int, VaultService>
     */
    public function list(ListServicesQuery $query): Collection
    {
        $builder = VaultService::query()
            ->where('user_id', $query->userId)
            ->orderByDesc('created_at');

        if ($query->type !== null && $query->type !== '') {
            $builder->where('type', $query->type);
        }

        if ($query->status !== null && $query->status !== '') {
            $builder->where('status', $query->status);
        }

        if ($query->search !== null && $query->search !== '') {
            $builder->where(function ($inner) use ($query): void {
                $inner->where('name', 'like', "%{$query->search}%")
                    ->orWhere('data', 'like', "%{$query->search}%");
            });
        }

        return $builder->get();
    }
}

