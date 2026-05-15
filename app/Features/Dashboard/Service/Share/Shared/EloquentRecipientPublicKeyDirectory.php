<?php

namespace App\Features\Dashboard\Service\Share\Shared;

use App\Models\User;
use Illuminate\Support\Str;

final class EloquentRecipientPublicKeyDirectory implements RecipientPublicKeyDirectory
{
    public function findByEmail(string $email): ?RecipientPublicKey
    {
        $normalizedEmail = Str::lower(trim($email));
        if ($normalizedEmail === '') {
            return null;
        }

        $user = User::query()
            ->select(['id', 'email', 'public_key'])
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();

        if ($user === null) {
            return null;
        }

        return new RecipientPublicKey(
            userId: (string) $user->id,
            email: (string) $user->email,
            publicKey: (string) $user->public_key,
        );
    }
}
