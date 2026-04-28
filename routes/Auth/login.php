<?php

use App\Features\Auth\Login\Authenticate\AuthenticateController;
use App\Features\Auth\Login\Identify\IdentifyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('auth/login/pages/LoginPage');
})->middleware('guest')->name('login');

Route::post('/login', IdentifyController::class)->name('login.perform')->middleware('throttle:login');
Route::get('/login/authenticate', AuthenticateController::class)->name('login.authenticate')->middleware('pending.mfa');
