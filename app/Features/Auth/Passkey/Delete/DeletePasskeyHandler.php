<?php

namespace App\Features\Auth\Passkey\Delete;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Validation\ValidationException;

class DeletePasskeyHandler
{
    #[CommandHandler]
    public function handle(DeletePasskeyCommand $command): void
    {
        $user = User::query()->find($command->userId);

        if (!$user) {
            throw ValidationException::withMessages([
                'passkey' => 'Unable to remove passkey. Please sign in again.',
            ]);
        }

        $user->passkeys()->where('id', $command->passkeyId)->delete();
    }
}
