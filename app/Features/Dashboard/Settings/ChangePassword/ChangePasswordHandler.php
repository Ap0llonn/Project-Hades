<?php

namespace App\Features\Dashboard\Settings\ChangePassword;

use App\Models\KeyWrapper;
use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class ChangePasswordHandler
{
    #[CommandHandler]
    public function handle(ChangePasswordCommand $command): void
    {
        /** @var User $user */
        $user = User::query()->findOrFail($command->userId);

        if (!Hash::check($command->currentPassword, $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        if (Hash::check($command->newPassword, $user->password_hash)) {
            throw ValidationException::withMessages([
                'password' => ['New password must be different from your current password.'],
            ]);
        }

        DB::transaction(function () use ($command, $user): void {
            $activePasswordWrapper = $this->resolveActivePasswordWrapper($user);
            $nextWrapperPayload = $this->normalizeWrappedDekPayload($command->wrappedDek);

            KeyWrapper::query()
                ->where('vault_id', $activePasswordWrapper->vault_id)
                ->where('type', 'password')
                ->whereNull('revoked_at')
                ->update([
                    'revoked_at' => now(),
                    'updated_at' => now(),
                ]);

            KeyWrapper::query()->create([
                'vault_id' => $activePasswordWrapper->vault_id,
                'type' => 'password',
                'ciphertext' => $nextWrapperPayload['ciphertext'],
                'nonce' => $nextWrapperPayload['nonce'],
                'tag' => null,
                'prf_salt' => $nextWrapperPayload['salt'],
                'prf_params' => [
                    'algorithm' => $nextWrapperPayload['algorithm'],
                    'opsLimit' => $nextWrapperPayload['opsLimit'],
                    'memoryKb' => $nextWrapperPayload['memoryKb'],
                    'type' => $nextWrapperPayload['type'],
                    'keyLengthBits' => $nextWrapperPayload['keyLengthBits'],
                ],
                'credential_id' => null,
                'metadata' => [
                    'purpose' => 'dek_wrapper',
                ],
                'revoked_at' => null,
            ]);

            $user->password_hash = Hash::make($command->newPassword);
            $user->kdf_salt = $nextWrapperPayload['salt'];
            $user->kdf_params = [
                'algorithm' => $nextWrapperPayload['algorithm'],
                'opsLimit' => $nextWrapperPayload['opsLimit'],
                'memoryKb' => $nextWrapperPayload['memoryKb'],
                'type' => $nextWrapperPayload['type'],
                'keyLengthBits' => $nextWrapperPayload['keyLengthBits'],
            ];
            $user->save();
        });
    }

    private function resolveActivePasswordWrapper(User $user): KeyWrapper
    {
        $vault = $user->vault()->first();
        if ($vault === null) {
            throw ValidationException::withMessages([
                'password' => ['Vault not found for current user.'],
            ]);
        }

        $wrapper = KeyWrapper::query()
            ->where('vault_id', $vault->id)
            ->where('type', 'password')
            ->whereNull('revoked_at')
            ->orderByDesc('created_at')
            ->first();

        if ($wrapper === null) {
            throw ValidationException::withMessages([
                'password' => ['Password wrapper not found for current user.'],
            ]);
        }

        return $wrapper;
    }

    /**
     * @param array<string, mixed> $wrappedDek
     * @return array{
     *   ciphertext: string,
     *   nonce: string,
     *   salt: string,
     *   algorithm: string,
     *   opsLimit: int,
     *   memoryKb: int,
     *   type: string,
     *   keyLengthBits: int
     * }
     */
    private function normalizeWrappedDekPayload(array $wrappedDek): array
    {
        $kdf = is_array($wrappedDek['kdf'] ?? null) ? $wrappedDek['kdf'] : [];
        $algorithm = trim((string) ($kdf['algorithm'] ?? ''));
        $type = trim((string) ($kdf['type'] ?? ''));
        $ciphertext = trim((string) ($wrappedDek['ciphertext'] ?? ''));
        $nonce = trim((string) ($wrappedDek['iv'] ?? ''));
        $salt = trim((string) ($wrappedDek['salt'] ?? ''));
        $opsLimit = $this->toPositiveInt($kdf['opsLimit'] ?? null, 3);
        $memoryKb = $this->toPositiveInt($kdf['memoryKb'] ?? null, 65536);
        $keyLengthBits = $this->toPositiveInt($wrappedDek['keyLengthBits'] ?? null, 256);

        if ($algorithm === '' || $type === '' || $ciphertext === '' || $nonce === '' || $salt === '') {
            throw ValidationException::withMessages([
                'wrapped_dek' => ['Wrapped DEK payload is invalid.'],
            ]);
        }

        if (!in_array($algorithm, ['argon2id13', 'argon2i13'], true)
            || !in_array($type, ['Argon2id13', 'Argon2i13'], true)) {
            throw ValidationException::withMessages([
                'wrapped_dek' => ['Wrapped DEK KDF metadata is invalid.'],
            ]);
        }

        return [
            'ciphertext' => $ciphertext,
            'nonce' => $nonce,
            'salt' => $salt,
            'algorithm' => $algorithm,
            'opsLimit' => $opsLimit,
            'memoryKb' => $memoryKb,
            'type' => $type,
            'keyLengthBits' => $keyLengthBits,
        ];
    }

    private function toPositiveInt(mixed $value, int $fallback): int
    {
        $parsed = filter_var($value, FILTER_VALIDATE_INT);
        if (!is_int($parsed) || $parsed <= 0) {
            return $fallback;
        }

        return $parsed;
    }
}
