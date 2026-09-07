<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Allows the Next.js frontend (localhost:3000) to call this Laravel API.
    | In production, replace 'allowed_origins' with your real domain.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_merge(
        [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'https://operahotels.com',
            'https://www.operahotels.com',
            '*',
        ],
        explode(',', env('CORS_ALLOWED_ORIGINS', ''))
    )),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
