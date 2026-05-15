<?php

return [
    'code_ttl_seconds' => env('EXTENSION_AUTH_CODE_TTL_SECONDS', 120),
    'access_token_ttl_seconds' => env('EXTENSION_AUTH_ACCESS_TTL_SECONDS', 900),
    'refresh_token_ttl_seconds' => env('EXTENSION_AUTH_REFRESH_TTL_SECONDS', 2592000),
];

