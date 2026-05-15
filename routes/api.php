<?php

use App\Features\ExtensionAuth\ExchangeTokenController;
use App\Features\ExtensionAuth\MeController;
use App\Features\ExtensionAuth\RefreshTokenController;
use App\Features\ExtensionAuth\RevokeController;
use App\Features\Extension\Service\Create\CreateExtensionServiceController;
use Illuminate\Support\Facades\Route;

Route::domain('vault.vaultguardian.test')
    ->middleware(['vault.domain', 'throttle:extension-auth-token'])
    ->group(function (): void {
        Route::post('/extension/auth/token', ExchangeTokenController::class)->name('api.extension.auth.token');
        Route::post('/extension/auth/refresh', RefreshTokenController::class)->name('api.extension.auth.refresh');

        Route::middleware('extension.token')->group(function (): void {
            Route::get('/extension/auth/me', MeController::class)->name('api.extension.auth.me');
            Route::post('/extension/auth/revoke', RevokeController::class)->name('api.extension.auth.revoke');
            Route::post('/extension/services', CreateExtensionServiceController::class)->name('api.extension.services.create');
        });
    });
