<?php

use App\Features\Auth\Register\FinishProcess\FinishAccountPageController;
use App\Features\Auth\Register\FinishProcess\RegisterUserController;
use App\Features\Auth\Register\StartProcess\StartAccountController;
use App\Features\Auth\Register\StartProcess\StartAccountPageController;
use Illuminate\Support\Facades\Route;

Route::get('/finish-account', FinishAccountPageController::class)
    ->middleware('signed')
    ->name('finish-account');
Route::post('/finish-account', RegisterUserController::class)->name('finish-account.perform');
