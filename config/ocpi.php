<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    'database' => [
        'connection' => env('OCPI_DATABASE_CONNECTION'),
        'table' => [
            'prefix' => env('OCPI_DATABASE_TABLE_PREFIX', 'ocpi_'),
        ],
    ],

];
