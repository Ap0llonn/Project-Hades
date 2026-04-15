<?php

use App\Features\Auth\Login\LoginController;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('auth/pages/LoginPage');
})->name('login');

Route::post('/login', LoginController::class)->name('login.perform');

require __DIR__ . '/email.php';
require __DIR__ . '/StartAccount/startAccount.php';
