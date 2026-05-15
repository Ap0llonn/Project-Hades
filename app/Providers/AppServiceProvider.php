<?php

namespace App\Providers;

use App\Features\Dashboard\Service\Share\Shared\EloquentRecipientPublicKeyDirectory;
use App\Features\Dashboard\Service\Share\Shared\RecipientPublicKeyDirectory;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RecipientPublicKeyDirectory::class, EloquentRecipientPublicKeyDirectory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower(trim($request->input('email')));
            return Limit::perMinute(5)
                ->by($email . '|' . $request->ip())
                ->response(function () {
                    throw ValidationException::withMessages([
                        'email' => ['Too many login attempts. Please try again in a minute.'],
                    ]);
                });
        });

        RateLimiter::for('mfa-challenge-verify', function (Request $request) {
            $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');

            return Limit::perMinute(5)
                ->by($pendingUserId . '|' . $request->ip())
                ->response(function () {
                    throw ValidationException::withMessages([
                        'code' => ['Too many MFA verification attempts. Please wait a minute and try again.'],
                    ]);
                });
        });

        RateLimiter::for('mfa-email-challenge', function (Request $request) {
            $pendingUserId = (string) $request->session()->get('auth.pending_user_id', '');

            return Limit::perMinute(6)
                ->by($pendingUserId . '|' . $request->ip())
                ->response(function () {
                    throw ValidationException::withMessages([
                        'code' => ['Too many email code requests. Please wait a minute and try again.'],
                    ]);
                });
        });

        RateLimiter::for('mfa-email-setup', function (Request $request) {
            $user = $request->user();
            $userId = $user ? (string) $user->id : '';

            return Limit::perMinute(6)
                ->by($userId . '|' . $request->ip())
                ->response(function () {
                    throw ValidationException::withMessages([
                        'code' => ['Too many setup code requests. Please wait a minute and try again.'],
                    ]);
                });
        });

        RateLimiter::for('extension-auth-code', function (Request $request) {
            $user = $request->user();
            $userId = $user ? (string) $user->id : 'guest';

            return Limit::perMinute(12)->by($userId . '|' . $request->ip());
        });

        RateLimiter::for('extension-auth-token', function (Request $request) {
            $key = (string) ($request->input('code') ?? $request->input('refresh_token') ?? 'no-token');
            return Limit::perMinute(40)->by($request->ip() . '|' . sha1($key));
        });
    }
}
