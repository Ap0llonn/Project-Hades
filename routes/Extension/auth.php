<?php

use App\Features\ExtensionAuth\Authorize\AuthorizeExtensionAuthController;
use App\Features\ExtensionAuth\CreateCodeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:extension-auth-code'])->group(function (): void {
    Route::get('/extension/auth/authorize', AuthorizeExtensionAuthController::class)->name('extension.auth.authorize');
    Route::post('/extension/auth/code', CreateCodeController::class)->name('extension.auth.code');
});
