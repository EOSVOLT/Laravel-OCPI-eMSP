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
            'uri_prefix' => env('OCPI_SERVER_ROUTING_URI_PREFIX', 'ocpi/emsp'),
            'name_prefix' => env('OCPI_SERVER_ROUTING_NAME_PREFIX', 'ocpi.emsp.'),
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

];
