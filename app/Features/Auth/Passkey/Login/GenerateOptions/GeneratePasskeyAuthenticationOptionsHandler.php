<?php

namespace App\Features\Auth\Passkey\Login\GenerateOptions;

use Ecotone\Modelling\Attribute\CommandHandler;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;

class GeneratePasskeyAuthenticationOptionsHandler
{
    public function __construct(
        private readonly GeneratePasskeyAuthenticationOptionsAction $generatePasskeyAuthenticationOptionsAction,
    ) {
    }

    #[CommandHandler]
    public function handle(
        GeneratePasskeyAuthenticationOptionsCommand $command
    ): GeneratePasskeyAuthenticationOptionsResult {
        config()->set('passkeys.relying_party.id', $command->hostName);
        config()->set('app.url', $command->appUrl);

        $options = $this->generatePasskeyAuthenticationOptionsAction->execute();

        return new GeneratePasskeyAuthenticationOptionsResult($options);
    }
}
