<?php

use App\Features\Auth\Login\LoginController;
use App\Features\Auth\EmailValidation\EmailVerificationController;
use App\Features\Auth\Register\RegisterUserController;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('auth/pages/LoginPage');
})->name('login');

Route::post('/login', LoginController::class)->name('login.perform');

Route::get('/signup', function () {
    return Inertia::render('auth/pages/SignupPage');
})->name('signup');

Route::post('/signup', RegisterUserController::class)->name('signup.perform');

require __DIR__ . '/email.php';
