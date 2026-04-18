<?php

namespace App\Providers;

use Illuminate\Http\Request;
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
        //
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
    }
}
