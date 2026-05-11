<?php

use App\Features\Auth\OAuth\Link\Complete\CompleteOAuthLinkController;
use App\Features\Auth\OAuth\Link\Start\StartOAuthLinkController;
use App\Features\Auth\OAuth\Link\Unlink\UnlinkOAuthLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('/settings/security/oauth')
    ->middleware('auth')
    ->group(function (): void {
        Route::get('/link/{provider}', StartOAuthLinkController::class)
            ->whereIn('provider', ['google', 'apple'])
            ->name('settings.security.oauth.link');

        Route::match(['get', 'post'], '/callback/{provider}', CompleteOAuthLinkController::class)
            ->whereIn('provider', ['google', 'apple'])
            ->name('settings.security.oauth.callback');

        Route::delete('/{provider}', UnlinkOAuthLinkController::class)
            ->whereIn('provider', ['google', 'apple'])
            ->name('settings.security.oauth.unlink');
    });
