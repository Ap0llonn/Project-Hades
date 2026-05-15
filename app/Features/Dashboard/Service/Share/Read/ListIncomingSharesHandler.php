<?php

namespace App\Features\Dashboard\Service\Share\Read;

use App\Models\ServiceShare;
use Illuminate\Database\Eloquent\Collection;

final class ListIncomingSharesHandler
{
    /**
     * @return Collection<int, ServiceShare>
     */
    public function handle(ListIncomingSharesQuery $query): Collection
    {
        $builder = ServiceShare::query()
            ->with([
                'service',
                'owner:id,email,public_key',
            ])
            ->where('recipient_user_id', $query->recipientUserId)
            ->orderByDesc('created_at');

        $builder->whereHas('service', function ($serviceBuilder) use ($query): void {
            if ($query->type !== null && $query->type !== '') {
                $serviceBuilder->where('type', $query->type);
            }

            if ($query->status !== null && $query->status !== '') {
                $serviceBuilder->where('status', $query->status);
            }

            if ($query->search !== null && $query->search !== '') {
                $serviceBuilder->where(function ($inner) use ($query): void {
                    $inner->where('name', 'like', "%{$query->search}%")
                        ->orWhere('data', 'like', "%{$query->search}%");
                });
            }
        });

        return $builder->get();
    }
}
