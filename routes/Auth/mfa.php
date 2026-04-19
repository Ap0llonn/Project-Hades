<?php

use App\Features\Auth\MFA\RecoveryTokens\RecoveryCodeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/mfa', function () {
    return Inertia::render('auth/mfa/pages/MfaPage');
})->name('mfa');

Route::get('/mfa/recovery-codes', function () {
    return Inertia::render('auth/mfa/pages/MfaRecoveryCodesPage');
})->middleware('auth')->name('mfa.recovery-codes');

Route::post('/mfa/recovery-codes', [RecoveryCodeController::class, 'initCodes'])->middleware('auth')->name('mfa.recovery-codes.send');
