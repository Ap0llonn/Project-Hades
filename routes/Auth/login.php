<?php

use App\Features\Auth\Login\LoginController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return Inertia::render('auth/pages/LoginPage');
})->name('login');

Route::post('/login', LoginController::class)->name('login.perform')->middleware('throttle:login');
