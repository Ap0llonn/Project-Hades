<?php

use App\Features\Auth\Login\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/Auth/auth.php';

Route::get('/terms-of-service', function () {
    return Inertia::render('shared/pages/TermsOfServicePage');
})->name('terms');

Route::get('/privacy-policy', function () {
    return Inertia::render('shared/pages/PrivacyPolicyPage');
})->name('privacy');

Route::domain('passwordmanager.test')->group(function () {
    require base_path('routes/Auth/StartAccount/startAccount.php');
    Route::get('/', function () {
        return Inertia::render('home/pages/HomePage');
    })->name('home');;
});

Route::domain('vault.passwordmanager.test')->group(function () {
    require base_path('routes/Auth/FinishAccount/register.php');
});
