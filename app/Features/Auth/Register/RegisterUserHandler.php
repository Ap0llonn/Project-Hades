<?php

namespace App\Features\Auth\Register;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RegisterUserHandler
{


    #[CommandHandler]
    public function handle(RegisterUserCommand $command): array
    {
        $email = Str::lower(trim($command->email));

        //maybe make a separate repo to handle DB logic
        try {
            User::create([
                'email' => $email,
                'email_hashed' => hash_hmac('sha256', $email, (string) config('app.key')),
                'first_name' => $command->firstName,
                'last_name' => $command->lastName,
                'master_key_wrapper' => $command->master_key_wrapper,
                'kdf_salt' => $command->kdf_salt,
                'kdf_params' => $command->kdf_params,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'email' => __('validation.unique', ['attribute' => 'email']),
                ]);
            }
            throw $exception;
        }

        Log::info("Registered user with email {$email}");
        return [];
    }
}
