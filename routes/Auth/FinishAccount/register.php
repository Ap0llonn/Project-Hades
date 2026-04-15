<?php

use App\Features\Auth\Register\FinishProcess\FinishAccountPageController;
use App\Features\Auth\Register\FinishProcess\RegisterUserController;
use App\Features\Auth\Register\StartProcess\StartAccountController;
use App\Features\Auth\Register\StartProcess\StartAccountPageController;

Route::get('/finish-account', FinishAccountPageController::class)->name('finish-account');
Route::post('/finish-account', RegisterUserController::class)->name('finish-account.perform');
