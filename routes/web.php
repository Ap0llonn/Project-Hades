<?php

use App\Features\Auth\Login\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return Inertia::render('home/pages/HomePage');
})->name('home');;

