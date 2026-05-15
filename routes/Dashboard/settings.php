<?php

use App\Features\Dashboard\Settings\ChangePassword\ChangePasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::put('/settings/password', ChangePasswordController::class)->name('settings.password.change');
});
