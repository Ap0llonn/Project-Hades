<?php

namespace App\Features\Dashboard\Service\Create;

use App\Models\VaultService;
use Ecotone\Modelling\Attribute\CommandHandler;

final class CreateServiceHandler
{
    #[CommandHandler]
    public function handle(CreateServiceCommand $command): VaultService
    {
        return VaultService::query()->create([
            'user_id' => $command->userId,
            'type' => $command->type,
            'name' => 'Encrypted Item',
            'favorite' => $command->favorite,
            'status' => $command->status,
            'data' => $command->payload,
        ]);
    }
}
