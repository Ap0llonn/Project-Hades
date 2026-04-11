<?php

use App\Features\Auth\Login\LoginController;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('auth/pages/LoginPage');
})->name('login');

Route::post('/login', LoginController::class)->name('login.perform');

Route::get('/signup', function () {
    return Inertia::render('auth/pages/SignupPage');
})->name('signup');
