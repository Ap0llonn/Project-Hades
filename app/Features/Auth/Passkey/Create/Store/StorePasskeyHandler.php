<?php

namespace App\Features\Auth\Passkey\Create\Store;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\DB;
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
        $user = User::query()->find($command->userId);

        if (!$user || !($user instanceof HasPasskeys)) {
            throw ValidationException::withMessages([
                'passkey' => 'Unable to register passkey. Please sign in again.',
            ]);
        }

        $passkeyName = trim((string) ($command->name ?? ''));
        if ($passkeyName === '') {
            $passkeyName = sprintf('%s passkey', $user->getPassKeyDisplayName());
        }

        config()->set('passkeys.relying_party.id', $command->hostName);
        config()->set('app.url', $command->appUrl);

        try {
            DB::transaction(function () use ($command, $passkeyName, $user): void {
                $this->storePasskeyAction->execute(
                    $user,
                    $command->passkeyJson,
                    $command->passkeyOptionsJson,
                    $command->hostName,
                    ['name' => Str::limit($passkeyName, 120, '')],
                );

                $credentialId = $this->extractCredentialIdFromPasskeyJson($command->passkeyJson);
                if ($credentialId === '') {
                    throw ValidationException::withMessages([
                        'passkey' => 'Passkey registration returned an invalid credential id.',
                    ]);
                }

                $createdPasskey = $user->passkeys()->latest('id')->first();
                if ($createdPasskey === null) {
                    throw ValidationException::withMessages([
                        'passkey' => 'Passkey registration succeeded but passkey record was not found.',
                    ]);
                }

                $passkeyUuid = (string) ($createdPasskey->uuid ?? '');
                if ($passkeyUuid === '') {
                    $passkeyUuid = (string) Str::uuid();
                    $createdPasskey->uuid = $passkeyUuid;
                    $createdPasskey->save();
                }

                $vault = $user->vault()->first();
                if ($vault === null) {
                    throw ValidationException::withMessages([
                        'passkey' => 'Vault not found for current user.',
                    ]);
                }

                DB::table('key_wrappers')
                    ->where('vault_id', $vault->id)
                    ->where('type', 'passkey')
                    ->where('passkey_uuid', $passkeyUuid)
                    ->whereNull('revoked_at')
                    ->update([
                        'revoked_at' => now(),
                        'updated_at' => now(),
                    ]);

                DB::table('key_wrappers')->insert([
                    'vault_id' => $vault->id,
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
