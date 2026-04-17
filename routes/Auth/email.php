<?php

use App\Features\Auth\EmailValidation\EmailVerificationController;
use App\Features\Auth\Register\StartProcess\StartAccountController;


Route::get('/email/verify', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');
