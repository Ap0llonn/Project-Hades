<?php

namespace App\Features\Auth\MFA\TOTP\VerifyChallenge;

use App\Models\MfaMethods;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use OTPHP\TOTP;

final class VerifyTotpChallengeHandler
{
    #[CommandHandler]
    public function handle(VerifyTotpChallengeCommand $command): void
    {
        $mfaMethods = MfaMethods::query()->where('user_id', $command->userId)->first();
        if (!$mfaMethods || !$mfaMethods->mfa_activated) {
            throw ValidationException::withMessages([
                'code' => 'MFA is not enabled for this account.',
            ]);
        }

        if ($command->method === 'email') {
            $this->verifyEmailCode($mfaMethods, $command->code);
            return;
        }

        if ($command->method === 'recovery') {
            $this->verifyRecoveryCode($mfaMethods, $command->code);
            return;
        }

        $this->verifyTotpCode($mfaMethods, $command->code);
    }

    private function verifyTotpCode(MfaMethods $mfaMethods, string $rawCode): void
    {
        if (!$mfaMethods->totp_secret) {
            throw ValidationException::withMessages([
                'code' => 'Authenticator app is not enabled for this account.',
            ]);
        }

        $code = preg_replace('/\D+/', '', $rawCode);
        if ($code === null || strlen($code) !== 6) {
            throw ValidationException::withMessages([
                'code' => 'Verification code must be exactly 6 digits.',
            ]);
        }

        $secret = trim((string) $mfaMethods->totp_secret);
        try {
            $secret = (string) decrypt($secret);
        } catch (DecryptException) {
        }

        $totp = TOTP::create($secret);
        if (!$totp->verify($code, null, 29)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid code. Check your device time and try the latest 6-digit code.',
            ]);
        }
    }

    private function verifyEmailCode(MfaMethods $mfaMethods, string $rawCode): void
    {
        if (!$mfaMethods->email_enabled) {
            throw ValidationException::withMessages([
                'code' => 'Email code verification is not enabled for this account.',
            ]);
        }

        $verificationState = Session::get('auth.pending_email_mfa');
        if (!is_array($verificationState)) {
            throw ValidationException::withMessages([
                'code' => 'No email verification code is pending. Request a new code.',
            ]);
        }

        $expiresAt = (int) ($verificationState['expires_at'] ?? 0);
        if ($expiresAt <= 0 || now()->timestamp > $expiresAt) {
            Session::forget('auth.pending_email_mfa');
            throw ValidationException::withMessages([
                'code' => 'Email verification code expired. Request a new code.',
            ]);
        }

        $normalizedCode = preg_replace('/\D+/', '', $rawCode);
        if ($normalizedCode === null || strlen($normalizedCode) !== 6) {
            throw ValidationException::withMessages([
                'code' => 'Verification code must be exactly 6 digits.',
            ]);
        }

        $expectedHash = (string) ($verificationState['code_hash'] ?? '');
        $providedHash = hash_hmac('sha256', $normalizedCode, (string) config('app.key'));
        if ($expectedHash === '' || !hash_equals($expectedHash, $providedHash)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid email verification code.',
            ]);
        }

        Session::forget('auth.pending_email_mfa');
    }

    private function verifyRecoveryCode(MfaMethods $mfaMethods, string $rawCode): void
    {
        $storedRecoveryCodes = $mfaMethods->recovery_codes ?? [];
        if (!is_array($storedRecoveryCodes) || $storedRecoveryCodes === []) {
            throw ValidationException::withMessages([
                'code' => 'Recovery codes are not available for this account.',
            ]);
        }

        $normalizedCode = strtoupper(trim($rawCode));
        if ($normalizedCode === '') {
            throw ValidationException::withMessages([
                'code' => 'Recovery code is required.',
            ]);
        }

        $hashedCode = base64_encode(hash('sha256', $normalizedCode, true));
        $index = array_search($hashedCode, $storedRecoveryCodes, true);
        if ($index === false) {
            throw ValidationException::withMessages([
                'code' => 'Invalid recovery code.',
            ]);
        }

        unset($storedRecoveryCodes[$index]);
        $mfaMethods->recovery_codes = array_values($storedRecoveryCodes);
        $mfaMethods->save();
    }
}
