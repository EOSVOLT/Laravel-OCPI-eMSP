<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Party
    |--------------------------------------------------------------------------
    */

    'party' => [
        'party_id' => env('OCPI_EMSP_PARTY_ID'),
        'country_code' => env('OCPI_EMSP_COUNTRY_CODE'),
        'business_details' => [
            'name' => env('OCPI_EMSP_NAME', env('APP_NAME')),
            'website' => env('OCPI_EMSP_WEBSITE', env('APP_URL')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Versions
    |--------------------------------------------------------------------------
    */

    'versions' => [
        '2.1.1' => [
            'modules' => [
                'credentials',
            ],
        ],
    ],

];
