<?php

namespace App\Features\Dashboard\Service\Update;

use App\Models\VaultService;
use Ecotone\Modelling\Attribute\CommandHandler;

final class UpdateServiceHandler
{
    #[CommandHandler]
    public function handle(UpdateServiceCommand $command): ?VaultService
    {
        $service = VaultService::query()
            ->where('id', $command->serviceId)
            ->where('user_id', $command->userId)
            ->first();

        if ($service === null) {
            return null;
        }

        $service->fill($command->changes);
        $service->save();

        return $service->fresh();
    }
}

