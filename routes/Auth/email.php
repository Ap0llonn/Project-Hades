<?php

use App\Features\Auth\EmailValidation\EmailVerificationController;

Route::get('/email-confirmation', [EmailVerificationController::class, 'confirmation'])
    ->name('email.confirmation');

Route::get('/email/verify', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

Route::get('/email-verified', [EmailVerificationController::class, 'success'])
    ->name('verification.success');
