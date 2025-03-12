# Laravel OCPI eMSP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codivores/laravel-ocpi-emsp.svg?style=flat-square)](https://packagist.org/packages/codivores/laravel-ocpi-emsp)
[![Total Downloads](https://img.shields.io/packagist/dt/codivores/laravel-ocpi-emsp.svg?style=flat-square)](https://packagist.org/packages/codivores/laravel-ocpi-emsp)

Laravel package for OCPI ([Open Charge Point Interface](https://github.com/ocpi/ocpi)) protocol as eMSP (e-Mobility Service Provider).

### Key Features:

- **OCPI version:** 2.1.1
- **OCPI Modules:**
  - CDRs
  - Commands
  - Credentials & Registration
  - Locations
  - Sessions
  - Versions

### Version support

- **PHP:** `8.2`, `8.3`
- **Laravel:** `11.0`

## Installation

You can install the package via composer:

```bash
composer require codivores/laravel-ocpi-emsp
```

If you want to customize the eMSP configuration (party information, versions and available modules), you can publish the dedicated config file:

```bash
php artisan vendor:publish --tag="ocpi-emsp-config"
```

This is the content of the published config file:

```php
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
                'cdrs',
                'commands',
                'credentials',
                'locations',
                'sessions',
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
            'id_separator' => env('OCPI_EMSP_MODULE_CDRS_ID_SEPARATOR', '___'),
        ],
    ],

];
```

If you want to customize the package configuration, you can publish the config file:

```bash
php artisan vendor:publish --tag="ocpi-config"
```

This is the content of the published config file:

```php
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
    | Client
    |--------------------------------------------------------------------------
    */

    'client' => [
        'server' => [
            'url' => env('OCPI_CLIENT_SERVER_URL', env('APP_URL')).'/'.env('OCPI_SERVER_ROUTING_URI_PREFIX', 'ocpi/emsp'),
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
```

## Getting started

#### Define the eMSP information environment variables:

```dotenv
OCPI_EMSP_PARTY_ID=MYC
OCPI_EMSP_COUNTRY_CODE=FR
OCPI_EMSP_NAME=My Company
OCPI_EMSP_WEBSITE=https://www.my-company.org
```

#### Initialize a new "Sender" Party to start credentials exchange:

```bash
php artisan ocpi:credentials:initialize
```

#### Run credentials exchange with a new "Sender" Party:

```bash
php artisan ocpi:credentials:register {party_code}
```
## Other commands

#### Update credentials and versions with a Party:

```bash
php artisan ocpi:credentials:update {party_code} {--without_new_client_token}
```

#### Synchronize locations of all or a specific Party:

```bash
php artisan ocpi:locations:synchronize {--P|party=}
```


## License

The DBAD License (DBAD). Please see [License File](LICENSE.md) for more information.
