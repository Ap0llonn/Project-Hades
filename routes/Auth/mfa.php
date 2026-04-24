<?php

use App\Features\Auth\MFA\RecoveryCodes\Store\RecoveryCodeController;
use App\Features\Auth\MFA\TOTP\Disable\DisableTotpController;
use App\Features\Auth\MFA\TOTP\Generate\GenerateTotpController;
use App\Features\Auth\MFA\TOTP\Page\TotpPageController;
use App\Features\Auth\MFA\TOTP\Verify\VerifyTotpController;
use Illuminate\Support\Facades\Route;

Route::get('/mfa', TotpPageController::class)->name('mfa');

Route::get('/mfa/recovery-codes',  [RecoveryCodeController::class, 'page'])->middleware('auth')->name('mfa.recovery-codes');

Route::post('/mfa/recovery-codes', [RecoveryCodeController::class, 'store'])->middleware('auth')->name('mfa.recovery-codes.send');
Route::post('/mfa/totp/setup-qr', GenerateTotpController::class)->middleware('auth')->name('mfa.totp.setup-qr');
Route::post('/mfa/totp/verify', VerifyTotpController::class)->middleware('auth')->name('mfa.totp.verify');
Route::post('/mfa/totp/disable', DisableTotpController::class)->middleware('auth')->name('mfa.totp.disable');
