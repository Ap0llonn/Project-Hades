<?php

use App\Features\Passwords\CreatePassword\CreatePasswordCommand;
use App\Features\Passwords\CreatePassword\CreatePasswordHandler;
use App\Features\Passwords\GetPasswordById\GetPasswordByIdHandler;
use App\Features\Passwords\GetPasswordById\GetPasswordByIdQuery;

return [
    'commands' => [
        CreatePasswordCommand::class => CreatePasswordHandler::class,
    ],
    'queries' => [
        GetPasswordByIdQuery::class => GetPasswordByIdHandler::class,
    ],
];

