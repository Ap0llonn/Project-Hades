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
        $credentialId = $this->extractCredentialId($command->startAuthenticationResponseJson);

        config()->set('passkeys.relying_party.id', $command->hostName);
        config()->set('app.url', $command->appUrl);

        $passkey = $this->findPasskeyToAuthenticateAction->execute(
            $command->startAuthenticationResponseJson,
            $command->passkeyAuthenticationOptionsJson,
        );

        if (!$passkey || !($passkey->authenticatable instanceof User)) {
            return new AuthenticateUsingPasskeyResult(null, null, null);
        }

        return new AuthenticateUsingPasskeyResult(
            $passkey->authenticatable,
            $credentialId !== '' ? $credentialId : null,
            is_string($passkey->uuid ?? null) ? $passkey->uuid : null,
        );
    }

    private function extractCredentialId(string $startAuthenticationResponseJson): string
    {
        $payload = json_decode($startAuthenticationResponseJson, true);
        if (!is_array($payload)) {
            return '';
        }

        $credentialId = $payload['id'] ?? '';

        return is_string($credentialId) ? trim($credentialId) : '';
    }
}
