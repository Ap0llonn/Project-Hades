<?php

namespace App\Features\Dashboard\Service\Share\Revoke;

use App\Models\ServiceShare;
use Ecotone\Modelling\Attribute\CommandHandler;

final class RevokeServiceShareHandler
{
    #[CommandHandler]
    public function handle(RevokeServiceShareCommand $command): bool
    {
        $share = ServiceShare::query()
            ->where('id', $command->shareId)
            ->where('service_id', $command->serviceId)
            ->where(function ($query) use ($command): void {
                $query->where('owner_user_id', $command->actorUserId)
                    ->orWhere('recipient_user_id', $command->actorUserId);
            })
            ->first();

        if ($share === null) {
            return false;
        }

        $share->delete();

        return true;
    }
}
