<?php

return [
    'proxy' => [
        'grant_type' => env('OAUTH_GRANT_TYPE'),
        'client_id' => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'scope' => env('OAUTH_SCOPE', '*'),
    ],
    'refresh_token' => [
        'grant_type' => 'refresh_token',
        'client_id' => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'scope' => env('OAUTH_SCOPE', '*'),
    ],
];
