<?php

namespace App\Shared\CQRS\Exceptions;

use RuntimeException;

final class HandlerNotRegistered extends RuntimeException
{
    public static function forMessage(string $messageClass): self
    {
        return new self("No handler registered for [{$messageClass}].");
    }
}

