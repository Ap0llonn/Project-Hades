<?php

use App\Features\Auth\Login\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/Auth/auth.php';

Route::domain('passwordmanager.test')->group(function () {
    require base_path('routes/Auth/StartAccount/startAccount.php');
    Route::get('/', function () {
        return Inertia::render('home/pages/HomePage');
    })->name('home');;
});

Route::domain('vault.passwordmanager.test')->group(function () {
    require base_path('routes/Auth/FinishAccount/register.php');
});
