<?php

namespace App\Shared\CQRS;

interface QueryHandler
{
    public function handle(Query $query): mixed;
}

