<?php

use App\Features\Auth\MFA\MfaValidation\MfaValidationController;
use App\Features\Auth\MFA\RecoveryCodes\Store\RecoveryCodeController;
use App\Features\Auth\MFA\TOTP\TotpController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/mfa', MfaValidationController::class)->name('mfa');

Route::get('/mfa/recovery-codes', function () {
    return Inertia::render('auth/mfa/pages/MfaRecoveryCodesPage');
})->middleware('auth')->name('mfa.recovery-codes');

Route::post('/mfa/recovery-codes', [RecoveryCodeController::class, 'store'])->middleware('auth')->name('mfa.recovery-codes.send');
Route::post('/mfa/totp/setup-qr', [TotpController::class, 'setupQr'])->middleware('auth')->name('mfa.totp.setup-qr');
Route::post('/mfa/totp/verify', [TotpController::class, 'verify'])->middleware('auth')->name('mfa.totp.verify');
