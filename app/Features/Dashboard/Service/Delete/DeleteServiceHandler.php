<?php

namespace App\Features\Dashboard\Service\Delete;

use App\Models\VaultService;
use Ecotone\Modelling\Attribute\CommandHandler;

final class DeleteServiceHandler
{
    #[CommandHandler]
    public function handle(DeleteServiceCommand $command): bool
    {
        $service = VaultService::query()
            ->where('id', $command->serviceId)
            ->where('user_id', $command->userId)
            ->first();

        if ($service === null) {
            return false;
        }

        $service->delete();

        return true;
    }
}

