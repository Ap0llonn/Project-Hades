<?php

namespace App\Features\ExtensionAuth\DirectLogin;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class DirectLoginHandler
{
    #[CommandHandler]
    public function handle(DirectLoginCommand $command): DirectLoginResult
    {
        $email = Str::lower(trim($command->email));
        $emailHash = hash_hmac('sha256', $email, (string) config('app.key'));

        /** @var User|null $user */
        $user = User::query()->with('mfa')->where('email_hashed', $emailHash)->first();

        if ($user === null || !Hash::check($command->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect.'],
            ]);
        }

        if ($user->mfa?->mfa_activated) {
            throw ValidationException::withMessages([
                'email' => ['Your account has MFA enabled. Please sign in via the browser.'],
            ]);
        }

        return new DirectLoginResult((string) $user->id);
    }
}
