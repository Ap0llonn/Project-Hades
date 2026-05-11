<?php

namespace App\Features\Auth\Passkey\Create\Start;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;

class CreateStartPasskeyHandler
{
    public function __construct(
        private readonly GeneratePasskeyRegisterOptionsAction $generatePasskeyRegisterOptionsAction,
    ) {
    }

    #[CommandHandler]
    public function handle(CreateStartPasskeyCommand $command): CreateStartPasskeyResult
    {
        $user = User::query()->find($command->userId);

        if (!$user || !($user instanceof HasPasskeys)) {
            throw ValidationException::withMessages([
                'passkey' => 'Unable to prepare passkey registration for this user.',
            ]);
        }

        config()->set('passkeys.relying_party.id', $command->hostName);
        config()->set('app.url', $command->appUrl);

        $options = $this->generatePasskeyRegisterOptionsAction->execute($user);

        return new CreateStartPasskeyResult($options);
    }
}
