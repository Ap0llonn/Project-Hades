<?php

use App\Features\Auth\Login\Authenticate\AuthenticateController;
use App\Features\Auth\Login\Identify\IdentifyController;
use App\Features\Auth\OAuth\Login\Complete\CompleteOAuthLoginController;
use App\Features\Auth\OAuth\Login\Start\StartOAuthLoginController;
use App\Features\Auth\Passkey\Login\Authenticate\AuthenticateUsingPasskeyController;
use App\Features\Auth\Passkey\Login\GenerateOptions\GeneratePasskeyAuthenticationOptionsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('auth/login/pages/LoginPage');
})->middleware('guest')->name('login');

Route::post('/login', IdentifyController::class)->name('login.perform')->middleware('throttle:login');
Route::get('/login/authenticate', AuthenticateController::class)->name('login.authenticate')->middleware('pending.mfa');
Route::get('/passkeys/authentication-options', GeneratePasskeyAuthenticationOptionsController::class)
    ->name('passkeys.authentication_options')
    ->middleware('guest');
Route::post('/passkeys/authenticate', AuthenticateUsingPasskeyController::class)
    ->name('passkeys.login')
    ->middleware(['guest', 'throttle:login']);

Route::get('/oauth/{provider}/redirect', StartOAuthLoginController::class)
    ->whereIn('provider', ['google', 'apple'])
    ->name('oauth.login.redirect')
    ->middleware(['guest', 'throttle:login']);

Route::match(['get', 'post'], '/oauth/{provider}/callback', CompleteOAuthLoginController::class)
    ->whereIn('provider', ['google', 'apple'])
    ->name('oauth.login.callback')
    ->middleware(['guest', 'throttle:login']);
