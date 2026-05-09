<?php

namespace App\Features\Auth\Passkey\Create\Store;

use App\Models\OAuthAccount;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Throwable;

class StorePasskeyHandler
{
    public function __construct(
        private readonly StorePasskeyAction $storePasskeyAction,
    ) {
    }

    #[CommandHandler]
    public function handle(StorePasskeyCommand $command): void
    {
        $user = $this->resolveUser($command->userId);
        $passkeyName = $this->resolvePasskeyName($command->name, $user);
        $this->setRuntimeConfig($command->hostName, $command->appUrl);

        try {
            DB::transaction(function () use ($command, $passkeyName, $user): void {
                $this->storePasskeyAndWrapper($command, $user, $passkeyName);
            });
        } catch (Throwable $exception) {
            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            report($exception);

            $message = __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey');
            if ((bool) config('app.debug')) {
                $message = sprintf('%s (%s)', $message, $exception->getMessage());
            }

            throw ValidationException::withMessages([
                'passkey' => $message,
            ]);
        }
    }

    private function resolveUser(string $userId): User
    {
        $user = User::query()->find($userId);

        if (!$user || !($user instanceof HasPasskeys)) {
            throw $this->passkeyValidationException('Unable to register passkey. Please sign in again.');
        }

        return $user;
    }

    private function resolvePasskeyName(?string $name, User $user): string
    {
        $passkeyName = trim((string) ($name ?? ''));
        if ($passkeyName !== '') {
            return $passkeyName;
        }

        return sprintf('%s passkey', $user->getPassKeyDisplayName());
    }

    private function setRuntimeConfig(string $hostName, string $appUrl): void
    {
        config()->set('passkeys.relying_party.id', $hostName);
        config()->set('app.url', $appUrl);
    }

    private function storePasskeyAndWrapper(StorePasskeyCommand $command, User $user, string $passkeyName): void
    {
        $this->storePasskeyAction->execute(
            $user,
            $command->passkeyJson,
            $command->passkeyOptionsJson,
            $command->hostName,
            ['name' => Str::limit($passkeyName, 120, '')],
        );

        $credentialId = $this->resolveCredentialId($command->passkeyJson);
        $passkeyUuid = $this->resolvePasskeyUuid($user);
        $vaultId = $this->resolveVaultId($user);

        $this->revokeExistingPasskeyWrappers($vaultId, $passkeyUuid);
        $this->insertPasskeyWrapper($command, $vaultId, $passkeyUuid, $credentialId);
        $this->markOAuthAccountsPasskeySetupAsCompleted((string) $user->id);
    }

    private function resolveCredentialId(string $passkeyJson): string
    {
        $credentialId = $this->extractCredentialIdFromPasskeyJson($passkeyJson);
        if ($credentialId !== '') {
            return $credentialId;
        }

        throw $this->passkeyValidationException('Passkey registration returned an invalid credential id.');
    }

    private function resolvePasskeyUuid(User $user): string
    {
        $createdPasskey = $user->passkeys()->latest('id')->first();
        if ($createdPasskey === null) {
            throw $this->passkeyValidationException(
                'Passkey registration succeeded but passkey record was not found.',
            );
        }

        $passkeyUuid = (string) ($createdPasskey->uuid ?? '');
        if ($passkeyUuid !== '') {
            return $passkeyUuid;
        }

        $passkeyUuid = (string) Str::uuid();
        $createdPasskey->uuid = $passkeyUuid;
        $createdPasskey->save();

        return $passkeyUuid;
    }

    private function resolveVaultId(User $user): mixed
    {
        $vault = $user->vault()->first();
        if ($vault !== null) {
            return $vault->id;
        }

        throw $this->passkeyValidationException('Vault not found for current user.');
    }

    private function revokeExistingPasskeyWrappers(mixed $vaultId, string $passkeyUuid): void
    {
        DB::table('key_wrappers')
            ->where('vault_id', $vaultId)
            ->where('type', 'passkey')
            ->where('passkey_uuid', $passkeyUuid)
            ->whereNull('revoked_at')
            ->update([
                'revoked_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function insertPasskeyWrapper(
        StorePasskeyCommand $command,
        mixed $vaultId,
        string $passkeyUuid,
        string $credentialId,
    ): void {
        DB::table('key_wrappers')->insert([
            'vault_id' => $vaultId,
            'type' => 'passkey',
            'ciphertext' => (string) ($command->wrappedDek['ciphertext'] ?? ''),
            'nonce' => (string) ($command->wrappedDek['iv'] ?? ''),
            'tag' => null,
            'prf_salt' => (string) ($command->wrappedDek['prf_salt'] ?? ''),
            'prf_params' => json_encode([
                'algorithm' => 'webauthn-prf',
                'outputLength' => $command->wrappedDek['prf_output_length'] ?? null,
                'deterministic' => true,
            ]),
            'credential_id' => $credentialId,
            'passkey_uuid' => $passkeyUuid,
            'metadata' => json_encode([
                'purpose' => 'dek_wrapper',
                'method' => 'passkey_prf',
            ]),
            'revoked_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function markOAuthAccountsPasskeySetupAsCompleted(string $userId): void
    {
        if (!Schema::hasTable('oauth_accounts')) {
            return;
        }

        OAuthAccount::query()
            ->where('user_id', $userId)
            ->whereNull('unlinked_at')
            ->get()
            ->each(function (OAuthAccount $account): void {
                $metadata = is_array($account->metadata) ? $account->metadata : [];
                $metadata['requires_passkey_setup'] = false;

                $account->metadata = $metadata;
                $account->save();
            });
    }

    private function passkeyValidationException(string $message): ValidationException
    {
        return ValidationException::withMessages([
            'passkey' => $message,
        ]);
    }

    private function extractCredentialIdFromPasskeyJson(string $passkeyJson): string
    {
        $payload = json_decode($passkeyJson, true);
        if (!is_array($payload)) {
            return '';
        }

        $credentialId = $payload['id'] ?? '';

        return is_string($credentialId) ? trim($credentialId) : '';
    }
}
