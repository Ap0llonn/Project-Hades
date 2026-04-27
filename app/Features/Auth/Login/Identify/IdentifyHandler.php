<?php

namespace App\Features\Auth\Login\Identify;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class IdentifyHandler
{

    #[CommandHandler]
    public function handle(IdentifyCommand $command): IdentifyResult
    {
        $email = Str::lower(trim($command->email));
        $emailHash = hash_hmac('sha256', $email, (string) config('app.key'));
        $user = User::where('email_hashed', $emailHash)->first();

        if (!$user || !Hash::check($command->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect'],
            ]);
        }

        return new IdentifyResult(
            (string) $user->id,
            (bool) $user->mfa->mfa_activated,
        );

    }
}
