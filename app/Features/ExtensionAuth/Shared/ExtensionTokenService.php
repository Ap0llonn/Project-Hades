<?php

namespace App\Features\ExtensionAuth\Shared;

use App\Models\ExtensionAuthCode;
use App\Models\ExtensionAuthToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ExtensionTokenService
{
    /**
     * @return array{code: string, expires_at: string}
     */
    public function issueAuthorizationCode(User $user, Request $request): array
    {
        $code = $this->generatePlainToken();
        $now = Carbon::now();
        $expiresAt = $now->copy()->addSeconds($this->codeTtlSeconds());

        ExtensionAuthCode::query()->create([
            'user_id' => (string) $user->id,
            'code_hash' => $this->hashToken($code),
            'expires_at' => $expiresAt,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return [
            'code' => $code,
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, token_type: string, expires_in: int, refresh_expires_in: int}
     */
    public function exchangeAuthorizationCode(string $authorizationCode, Request $request): array
    {
        $codeHash = $this->hashToken($authorizationCode);
        return DB::transaction(function () use ($codeHash, $request): array {
            $now = Carbon::now();

            /** @var ExtensionAuthCode|null $code */
            $code = ExtensionAuthCode::query()
                ->where('code_hash', $codeHash)
                ->whereNull('used_at')
                ->where('expires_at', '>', $now)
                ->lockForUpdate()
                ->first();

            if ($code === null) {
                throw new InvalidExtensionCredentialException('Invalid or expired authorization code.');
            }

            $code->used_at = $now;
            $code->save();

            return $this->issueTokenPairForUser((string) $code->user_id, $request, $now);
        });
    }

    /**
     * @return array{access_token: string, refresh_token: string, token_type: string, expires_in: int, refresh_expires_in: int}
     */
    public function refreshTokenPair(string $refreshToken, Request $request): array
    {
        $refreshHash = $this->hashToken($refreshToken);
        return DB::transaction(function () use ($refreshHash, $request): array {
            $now = Carbon::now();

            /** @var ExtensionAuthToken|null $record */
            $record = ExtensionAuthToken::query()
                ->where('refresh_token_hash', $refreshHash)
                ->whereNull('revoked_at')
                ->where('refresh_expires_at', '>', $now)
                ->lockForUpdate()
                ->first();

            if ($record === null) {
                throw new InvalidExtensionCredentialException('Invalid or expired refresh token.');
            }

            $record->last_refreshed_at = $now;
            $record->ip_address = $request->ip();
            $record->user_agent = $request->userAgent();
            $record->save();

            return $this->rotateTokenPair($record, $now);
        });
    }

    public function authenticateAccessToken(string $accessToken): ?User
    {
        $accessHash = $this->hashToken($accessToken);
        $now = Carbon::now();

        /** @var ExtensionAuthToken|null $token */
        $token = ExtensionAuthToken::query()
            ->with('user')
            ->where('access_token_hash', $accessHash)
            ->whereNull('revoked_at')
            ->where('access_expires_at', '>', $now)
            ->first();

        if ($token === null || $token->user === null) {
            return null;
        }

        $token->last_used_at = $now;
        $token->save();

        return $token->user;
    }

    public function revokeByAccessToken(string $accessToken): bool
    {
        $accessHash = $this->hashToken($accessToken);
        $record = ExtensionAuthToken::query()
            ->where('access_token_hash', $accessHash)
            ->whereNull('revoked_at')
            ->first();

        if ($record === null) {
            return false;
        }

        $record->revoked_at = Carbon::now();
        $record->save();

        return true;
    }

    /**
     * @return array{access_token: string, refresh_token: string, token_type: string, expires_in: int, refresh_expires_in: int, user: array{id: string, email: string, first_name: string|null, last_name: string|null}}
     */
    public function issueTokenPair(User $user, Request $request): array
    {
        $now = Carbon::now();
        $tokens = $this->issueTokenPairForUser((string) $user->id, $request, $now);

        $tokens['user'] = [
            'id'         => (string) $user->id,
            'email'      => (string) $user->email,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
        ];

        return $tokens;
    }

    /**
     * @return array{access_token: string, refresh_token: string, token_type: string, expires_in: int, refresh_expires_in: int}
     */
    private function issueTokenPairForUser(string $userId, Request $request, Carbon $now): array
    {
        $accessToken = $this->generatePlainToken();
        $refreshToken = $this->generatePlainToken();
        $accessTtlSeconds = $this->accessTokenTtlSeconds();
        $refreshTtlSeconds = $this->refreshTokenTtlSeconds();

        ExtensionAuthToken::query()->create([
            'user_id' => $userId,
            'access_token_hash' => $this->hashToken($accessToken),
            'refresh_token_hash' => $this->hashToken($refreshToken),
            'access_expires_at' => $now->copy()->addSeconds($accessTtlSeconds),
            'refresh_expires_at' => $now->copy()->addSeconds($refreshTtlSeconds),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $accessTtlSeconds,
            'refresh_expires_in' => $refreshTtlSeconds,
        ];
    }

    /**
     * @return array{access_token: string, refresh_token: string, token_type: string, expires_in: int, refresh_expires_in: int}
     */
    private function rotateTokenPair(ExtensionAuthToken $record, Carbon $now): array
    {
        $accessToken = $this->generatePlainToken();
        $refreshToken = $this->generatePlainToken();
        $accessTtlSeconds = $this->accessTokenTtlSeconds();
        $refreshTtlSeconds = $this->refreshTokenTtlSeconds();

        $record->access_token_hash = $this->hashToken($accessToken);
        $record->refresh_token_hash = $this->hashToken($refreshToken);
        $record->access_expires_at = $now->copy()->addSeconds($accessTtlSeconds);
        $record->refresh_expires_at = $now->copy()->addSeconds($refreshTtlSeconds);
        $record->save();

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $accessTtlSeconds,
            'refresh_expires_in' => $refreshTtlSeconds,
        ];
    }

    private function generatePlainToken(): string
    {
        return Str::random(96);
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    private function codeTtlSeconds(): int
    {
        return max(30, (int) config('extension_auth.code_ttl_seconds', 120));
    }

    private function accessTokenTtlSeconds(): int
    {
        return max(60, (int) config('extension_auth.access_token_ttl_seconds', 900));
    }

    private function refreshTokenTtlSeconds(): int
    {
        return max(300, (int) config('extension_auth.refresh_token_ttl_seconds', 2592000));
    }
}
