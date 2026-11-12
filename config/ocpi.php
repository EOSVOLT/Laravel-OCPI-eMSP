<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    */

    'server' => [
        'enabled' => env('OCPI_SERVER_ENABLED', true),
        'routing' => [
            'cpo' => [
                'uri_prefix' => env('OCPI_CPO_SERVER_ROUTING_URI_PREFIX', 'ocpi/cpo'),
                'name_prefix' => env('OCPI_CPO_SERVER_ROUTING_NAME_PREFIX', 'ocpi.cpo.')
            ],
            'emsp' => [
                'uri_prefix' => env('OCPI_SERVER_ROUTING_URI_PREFIX', 'ocpi/emsp'),
                'name_prefix' => env('OCPI_SERVER_ROUTING_NAME_PREFIX', 'ocpi.emsp.')
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    'client' => [
        'server' => [
            'url' => env('OCPI_CLIENT_SERVER_URL', env('APP_URL')).'/ocpi',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    'database' => [
        'connection' => env('OCPI_DATABASE_CONNECTION', env('DB_CONNECTION', 'sqlite')),
        'table' => [
            'prefix' => env('OCPI_DATABASE_TABLE_PREFIX', 'ocpi_'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        // Maximum number of characters of a response body written to the ocpi log, 0 = no limit.
        'response_body_max_length' => (int) env('OCPI_LOG_RESPONSE_BODY_MAX_LENGTH', 10000),
    ],

];
