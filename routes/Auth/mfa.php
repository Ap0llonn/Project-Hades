<?php

use App\Features\Auth\MFA\Email\Generate\GenerateEmailController;
use App\Features\Auth\MFA\Email\Challenge\RequestEmailChallengeController;
use App\Features\Auth\MFA\Email\Disable\DisableEmailController;
use App\Features\Auth\MFA\Email\Verify\EmailVerifyController;
use App\Features\Auth\MFA\RecoveryCodes\Store\RecoveryCodeController;
use App\Features\Auth\Passkey\Create\Start\CreateStartPasskeyController;
use App\Features\Auth\Passkey\Create\Store\StorePasskeyController;
use App\Features\Auth\Passkey\Delete\DeletePasskeyController;
use App\Features\Auth\MFA\TOTP\Disable\DisableTotpController;
use App\Features\Auth\MFA\TOTP\Generate\GenerateTotpController;
use App\Features\Auth\MFA\TOTP\Page\TotpPageController;
use App\Features\Auth\MFA\TOTP\Verify\VerifyTotpController;
use App\Features\Auth\MFA\TOTP\VerifyChallenge\VerifyTotpChallengeController;
use Illuminate\Support\Facades\Route;

Route::get('/mfa', TotpPageController::class)->name('mfa')->middleware('pending.mfa');
Route::post('/mfa/totp/verify-challenge', VerifyTotpChallengeController::class)->middleware(['pending.mfa', 'throttle:mfa-challenge-verify'])->name('mfa.totp.verify-challenge');
Route::post('/mfa/email/request-challenge', RequestEmailChallengeController::class)->middleware(['pending.mfa', 'throttle:mfa-email-challenge'])->name('mfa.email.request-challenge');

Route::get('/mfa/recovery-codes',  [RecoveryCodeController::class, 'page'])->middleware('auth')->name('mfa.recovery-codes');

Route::post('/mfa/recovery-codes', [RecoveryCodeController::class, 'store'])->middleware('auth')->name('mfa.recovery-codes.send');
Route::post('/mfa/totp/setup-qr', GenerateTotpController::class)->middleware('auth')->name('mfa.totp.setup-qr');
Route::post('/mfa/totp/verify-setup', VerifyTotpController::class)->middleware('auth')->name('mfa.totp.verify-setup');
Route::post('/mfa/totp/disable', DisableTotpController::class)->middleware('auth')->name('mfa.totp.disable');

Route::post('/mfa/email/generate', GenerateEmailController::class)->middleware(['auth', 'throttle:mfa-email-setup'])->name('mfa.email.generate');
Route::post('/mfa/email/verify', EmailVerifyController::class)->middleware('auth')->name('mfa.email.verify');
Route::post('/mfa/email/disable', DisableEmailController::class)->middleware('auth')->name('mfa.email.disable');

Route::prefix('/settings/security/passkeys')
    ->middleware('auth')
    ->group(function () {
        Route::get('/options', CreateStartPasskeyController::class)->name('settings.security.passkeys.options');
        Route::post('/', StorePasskeyController::class)->name('settings.security.passkeys.store');
        Route::delete('/{passkeyId}', DeletePasskeyController::class)->name('settings.security.passkeys.destroy');
    });
