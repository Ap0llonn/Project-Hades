<?php

namespace App\Features\Dashboard\Service\Share\Create;

use App\Models\ServiceShare;
use App\Models\VaultService;
use Ecotone\Modelling\Attribute\CommandHandler;

final class ShareServiceHandler
{
    #[CommandHandler]
    public function handle(ShareServiceCommand $command): ShareServiceResult
    {
        $service = VaultService::query()
            ->where('id', $command->serviceId)
            ->where('user_id', $command->ownerUserId)
            ->first();

        if ($service === null) {
            return ShareServiceResult::serviceNotFound();
        }

        $share = ServiceShare::query()->updateOrCreate(
            [
                'service_id' => $command->serviceId,
                'recipient_user_id' => $command->recipientUserId,
            ],
            [
                'owner_user_id' => $command->ownerUserId,
                'key_envelope' => $command->keyEnvelope,
            ],
        );

        return $share->wasRecentlyCreated
            ? ShareServiceResult::created($share)
            : ShareServiceResult::updated($share);
    }
}
