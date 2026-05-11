<?php

use App\Features\Auth\Dek\DekController;
use Illuminate\Support\Facades\Route;

Route::get('/vault/bootstrap', DekController::class)
    ->middleware('auth')
    ->name('vault.bootstrap');
