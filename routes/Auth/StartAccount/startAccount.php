<?php

use App\Features\Auth\EmailValidation\EmailVerificationController;
use App\Features\Auth\Register\FinishProcess\FinishAccountPageController;
use App\Features\Auth\Register\FinishProcess\RegisterUserController;
use App\Features\Auth\Register\StartProcess\StartAccountController;
use App\Features\Auth\Register\StartProcess\StartAccountPageController;
use Illuminate\Support\Facades\Route;

Route::get('/start-account', StartAccountPageController::class)->name('start-account');
Route::post('/start-account', StartAccountController::class)->name('start-account.perform');

Route::get('/email-confirmation', [EmailVerificationController::class, 'confirmation'])
    ->name('email.confirmation');

Route::post('/email-confirmation/resend', StartAccountController::class)
    ->name('email.confirmation.resend');
