<?php

use App\Features\Dashboard\Settings\Profile\Update\UpdateProfileController;
use App\Features\Dashboard\Settings\Sessions\Read\ListActiveSessionsController;
use App\Features\Dashboard\Settings\Sessions\Revoke\RevokeSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/settings/sessions', ListActiveSessionsController::class)->name('settings.sessions.read');
    Route::put('/settings/profile', UpdateProfileController::class)->name('settings.profile.update');
    Route::delete('/settings/sessions/{sessionId}', RevokeSessionController::class)->name('settings.sessions.revoke');
});
