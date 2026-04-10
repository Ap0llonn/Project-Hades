<?php

namespace App\Shared\CQRS;

use App\Shared\CQRS\Exceptions\HandlerNotRegistered;
use Illuminate\Contracts\Container\Container;

final class IlluminateQueryBus implements QueryBus
{
    /**
     * @param array<class-string, class-string> $handlers
     */
    public function __construct(
        private readonly array $handlers,
        private readonly Container $container,
    ) {
    }

    public function ask(Query $query): mixed
    {
        $handler = $this->resolveHandler($query::class);

        return $handler->handle($query);
    }

    private function resolveHandler(string $queryClass): object
    {
        $handlerClass = $this->handlers[$queryClass] ?? null;

        if ($handlerClass === null) {
            throw HandlerNotRegistered::forMessage($queryClass);
        }

        return $this->container->make($handlerClass);
    }
}

