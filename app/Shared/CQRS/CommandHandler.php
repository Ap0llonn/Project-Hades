<?php

namespace App\Shared\CQRS;

interface CommandHandler
{
    public function handle(Command $command): mixed;
}

