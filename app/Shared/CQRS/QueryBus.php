<?php

namespace App\Shared\CQRS;

interface QueryBus
{
    public function ask(Query $query): mixed;
}

