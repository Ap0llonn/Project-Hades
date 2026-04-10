<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/pages/HomePage');
});

Route::get('/login', function () {
    return Inertia::render('auth/pages/LoginPage');
})->name('login');

Route::get('/signup', function () {
    return Inertia::render('auth/pages/SignupPage');
})->name('signup');
