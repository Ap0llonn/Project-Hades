<?php

namespace App\Features\Auth\Passkey\Login\Authenticate;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;

class AuthenticateUsingPasskeyHandler
{
    public function __construct(
        private readonly FindPasskeyToAuthenticateAction $findPasskeyToAuthenticateAction,
    ) {
    }

    #[CommandHandler]
    public function handle(AuthenticateUsingPasskeyCommand $command): AuthenticateUsingPasskeyResult
    {
        config()->set('passkeys.relying_party.id', $command->hostName);
        config()->set('app.url', $command->appUrl);

        $passkey = $this->findPasskeyToAuthenticateAction->execute(
            $command->startAuthenticationResponseJson,
            $command->passkeyAuthenticationOptionsJson,
        );

        if (!$passkey || !($passkey->authenticatable instanceof User)) {
            return new AuthenticateUsingPasskeyResult(null);
        }

        return new AuthenticateUsingPasskeyResult($passkey->authenticatable);
    }
}
