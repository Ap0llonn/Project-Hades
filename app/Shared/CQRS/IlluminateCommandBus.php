<?php

namespace App\Shared\CQRS;

use App\Shared\CQRS\Exceptions\HandlerNotRegistered;
use Illuminate\Contracts\Container\Container;

final class IlluminateCommandBus implements CommandBus
{
    /**
     * @param array<class-string, class-string> $handlers
     */
    public function __construct(
        private readonly array $handlers,
        private readonly Container $container,
    ) {
    }

    public function dispatch(Command $command): mixed
    {
        $handler = $this->resolveHandler($command::class);

        return $handler->handle($command);
    }

    private function resolveHandler(string $commandClass): object
    {
        $handlerClass = $this->handlers[$commandClass] ?? null;

        if ($handlerClass === null) {
            throw HandlerNotRegistered::forMessage($commandClass);
        }

        return $this->container->make($handlerClass);
    }
}

