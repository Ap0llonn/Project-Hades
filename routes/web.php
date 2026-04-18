<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/terms-of-service', function () {
    return Inertia::render('shared/pages/TermsOfServicePage');
})->name('terms');

Route::get('/privacy-policy', function () {
    return Inertia::render('shared/pages/PrivacyPolicyPage');
})->name('privacy');

Route::domain('vaultguardian.test')
    ->middleware('marketing.domain')
    ->group(function () {
        require base_path('routes/Auth/StartAccount/startAccount.php');

        Route::get('/login', function () {
            return redirect()->route('login');
        });

        Route::get('/', function () {
            return Inertia::render('home/pages/HomePage');
        })->name('home');
    });

Route::domain('vault.vaultguardian.test')
    ->middleware('vault.domain')
    ->group(function () {
        require base_path('routes/Auth/email.php');
        require base_path('routes/Auth/FinishAccount/register.php');
        require base_path('routes/Auth/login.php');
        require base_path('routes/Auth/logout.php');

        Route::get('/dashboard', function () {
            return Inertia::render('dashboard/pages/DashboardPage');
        })->name('dashboard')->middleware('auth');

        Route::get('/settings', function () {
            return Inertia::render('dashboard/pages/SettingsPage');
        })->name('settings')->middleware('auth');

    });
