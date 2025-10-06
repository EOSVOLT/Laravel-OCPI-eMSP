<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Party
    |--------------------------------------------------------------------------
    */

    'party' => [
        'party_id' => env('OCPI_CPO_PARTY_ID'),
        'country_code' => env('OCPI_CPO_COUNTRY_CODE'),
        'business_details' => [
            'name' => env('OCPI_CPO_NAME', env('APP_NAME')),
            'website' => env('OCPI_CPO_WEBSITE', env('APP_URL')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Versions
    |--------------------------------------------------------------------------
    */

    'versions' => [
        '2.2.1' => [
            'modules' => [
                'cdrs',
                'commands',
                'credentials',
                'locations',
                'sessions',
                'tariffs',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    */

    'module' => [
        'cdrs' => [
            'id_separator' => env('OCPI_CPO_MODULE_CDRS_ID_SEPARATOR', '___'),
        ],
    ],

];
