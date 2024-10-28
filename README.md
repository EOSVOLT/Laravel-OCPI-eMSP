# Laravel OCPI eMSP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codivores/laravel-ocpi-emsp.svg?style=flat-square)](https://packagist.org/packages/codivores/laravel-ocpi-emsp)
[![Total Downloads](https://img.shields.io/packagist/dt/codivores/laravel-ocpi-emsp.svg?style=flat-square)](https://packagist.org/packages/codivores/laravel-ocpi-emsp)

Laravel package for OCPI ([Open Charge Point Interface](https://github.com/ocpi/ocpi)) protocol as eMSP (e-Mobility Service Provider).

### Key Features:

- **OCPI version:** 2.1.1
- **OCPI Modules:**
  - Versions
  - Credentials & Registration

### Version support

- **PHP:** `8.2`, `8.3`
- **Laravel:** `11.0`

## Installation

You can install the package via composer:

```bash
composer require codivores/laravel-ocpi-emsp
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
```

If you want to customize the eMSP configuration, you can publish the config file:

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
                'credentials',
            ],
        ],
    ],

];
```

## Getting started

Initialize a new "Sender" Party to start credentials exchange:

```bash
php artisan ocpi:credentials:initialize
```

Run credentials exchange with a new "Sender" Party':

```bash
php artisan ocpi:credentials:register {party_code}
```

## License

The DBAD License (DBAD). Please see [License File](LICENSE.md) for more information.
