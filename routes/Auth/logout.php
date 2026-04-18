<?php

use App\Features\Auth\logout\LogoutController;

Route::post('logout', LogoutController::class)->name('logout');
