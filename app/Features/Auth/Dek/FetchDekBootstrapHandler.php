<?php

namespace App\Features\Auth\Dek;

use App\Models\Vault;

final class FetchDekBootstrapHandler
{
    public function handle(FetchDekBootstrapQuery $query): FetchDekBootstrapView
    {
        $vault = Vault::query()
            ->where('user_id', $query->userId)
            ->with(['keyWrappers' => function ($builder) {
                $builder->whereNull('revoked_at')->orderByDesc('created_at');
            }])
            ->first();

        if ($vault === null) {
            return new FetchDekBootstrapView(false, null);
        }

        $selectedWrapper = null;
        foreach ($query->preferredWrapperTypes as $type) {
            if ($type === 'passkey' && $query->passkeyId !== '') {
                $selectedWrapper = $vault->keyWrappers
                    ->where('type', 'passkey')
                    ->firstWhere('passkey_uuid', $query->passkeyId);

                if ($selectedWrapper !== null) {
                    break;
                }
            }

            if ($type === 'passkey' && $query->passkeyCredentialId !== '') {
                $selectedWrapper = $vault->keyWrappers
                    ->where('type', 'passkey')
                    ->firstWhere('credential_id', $query->passkeyCredentialId);

                if ($selectedWrapper !== null) {
                    break;
                }
            }

            $selectedWrapper = $vault->keyWrappers->firstWhere('type', $type);
            if ($selectedWrapper !== null) {
                break;
            }
        }

        if ($selectedWrapper === null) {
            return new FetchDekBootstrapView(true, null);
        }

        return new FetchDekBootstrapView(true, new FetchDekBootstrapResult(
            primaryAuthMethod: $query->primaryAuthMethod,
            mfaAuthMethod: $query->mfaAuthMethod,
            wrappedPrivateKey: $query->privateKeyWrapper,
            dekWrapper: [
                'type' => $selectedWrapper->type,
                'ciphertext' => $selectedWrapper->ciphertext,
                'nonce' => $selectedWrapper->nonce,
                'tag' => $selectedWrapper->tag,
                'prf_salt' => $selectedWrapper->prf_salt,
                'prf_params' => $selectedWrapper->prf_params,
                'credential_id' => $selectedWrapper->credential_id,
                'metadata' => $selectedWrapper->metadata,
            ],
        ));
    }
}
