<?php

namespace App\Features\Auth\Passkey\Delete;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\DB;
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

        $passkeyQuery = $user->passkeys();

        $passkey = $passkeyQuery->where('uuid', $command->passkeyId)->first();
        if (!$passkey && ctype_digit($command->passkeyId)) {
            $passkey = $user->passkeys()->where('id', (int) $command->passkeyId)->first();
        }

        if (!$passkey) {
            return;
        }

        $vault = $user->vault()->first();
        if ($vault) {
            $credentialCandidates = array_values(array_filter([
                (string) $passkey->credential_id,
                $this->toBase64Url($passkey->data->publicKeyCredentialId ?? null),
            ]));

            $builder = DB::table('key_wrappers')
                ->where('vault_id', $vault->id)
                ->where('type', 'passkey')
                ->whereNull('revoked_at');

            $passkeyUuid = (string) ($passkey->uuid ?? '');
            if ($passkeyUuid !== '') {
                $builder->where(function ($query) use ($passkeyUuid, $credentialCandidates): void {
                    $query->where('passkey_uuid', $passkeyUuid);

                    if ($credentialCandidates !== []) {
                        $query->orWhereIn('credential_id', $credentialCandidates);
                    }
                });
            } else {
                $builder->whereIn('credential_id', $credentialCandidates);
            }

            $builder->update([
                'revoked_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $passkey->delete();
    }

    private function toBase64Url(?string $rawCredentialId): ?string
    {
        if ($rawCredentialId === null || $rawCredentialId === '') {
            return null;
        }

        return rtrim(strtr(base64_encode($rawCredentialId), '+/', '-_'), '=');
    }
}
