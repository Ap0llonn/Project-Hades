<?php

namespace App\Providers;

use App\Shared\CQRS\CommandBus;
use App\Shared\CQRS\IlluminateCommandBus;
use App\Shared\CQRS\IlluminateQueryBus;
use App\Shared\CQRS\QueryBus;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CommandBus::class, function (Container $container): CommandBus {
            /** @var array<class-string, class-string> $handlers */
            $handlers = config('cqrs.commands', []);

            return new IlluminateCommandBus($handlers, $container);
        });

        $this->app->singleton(QueryBus::class, function (Container $container): QueryBus {
            /** @var array<class-string, class-string> $handlers */
            $handlers = config('cqrs.queries', []);

            return new IlluminateQueryBus($handlers, $container);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
