<?php

namespace App\Features\Auth\Register\FinishProcess;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RegisterUserHandler
{


    #[CommandHandler]
    public function handle(RegisterUserCommand $command): void
    {
        $email = Str::lower(trim($command->email));

        //maybe make a separate repo to handle DB logic
        try {
            DB::transaction(function () use ($command, $email): void {
                $user = User::create([
                    'email' => $email,
                    'email_hashed' => hash_hmac('sha256', $email, (string) config('app.key')),
                    'password_hash' => Hash::make($command->password),
                    'first_name' => null,
                    'last_name' => null,
                    'private_key_wrapper' => [
                        'version' => 2,
                        'wrapped_private_key' => $command->wrapped_private_key,
                    ],
                    // Kept for backward compatibility with existing users columns.
                    'kdf_salt' => (string) ($command->wrapped_dek['salt'] ?? ''),
                    'kdf_params' => [
                        'algorithm' => $command->wrapped_dek['kdf']['algorithm'] ?? null,
                        'opsLimit' => $command->wrapped_dek['kdf']['opsLimit'] ?? null,
                        'memoryKb' => $command->wrapped_dek['kdf']['memoryKb'] ?? null,
                        'type' => $command->wrapped_dek['kdf']['type'] ?? null,
                        'keyLengthBits' => $command->wrapped_dek['keyLengthBits'] ?? null,
                    ],
                    'email_verified' => true,
                    'public_key' => $command->public_key,
                ]);

                $vaultId = DB::table('vault')->insertGetId([
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('key_wrappers')->insert([
                    'vault_id' => $vaultId,
                    'type' => 'password',
                    'ciphertext' => (string) ($command->wrapped_dek['ciphertext'] ?? ''),
                    'nonce' => (string) ($command->wrapped_dek['iv'] ?? ''),
                    'tag' => null,
                    'prf_salt' => (string) ($command->wrapped_dek['salt'] ?? ''),
                    'prf_params' => json_encode([
                        'algorithm' => $command->wrapped_dek['kdf']['algorithm'] ?? null,
                        'opsLimit' => $command->wrapped_dek['kdf']['opsLimit'] ?? null,
                        'memoryKb' => $command->wrapped_dek['kdf']['memoryKb'] ?? null,
                        'type' => $command->wrapped_dek['kdf']['type'] ?? null,
                        'keyLengthBits' => $command->wrapped_dek['keyLengthBits'] ?? null,
                    ]),
                    'credential_id' => null,
                    'metadata' => json_encode([
                        'purpose' => 'dek_wrapper',
                    ]),
                    'revoked_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'email' => __('validation.unique', ['attribute' => 'email']),
                ]);

            }
            throw $exception;
        }
    }
}
