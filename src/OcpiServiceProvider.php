<?php

declare(strict_types=1);

namespace Ocpi;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class OcpiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/ocpi.php',
            'ocpi'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/ocpi.php' => config_path('ocpi.php'),
        ], 'ocpi-config');

        app('config')
            ->set(
                'logging.channels.ocpi',
                [
                    'driver' => 'daily',
                    'path' => storage_path('logs/ocpi.log'),
                    'level' => env('OCPI_LOG_LEVEL', 'debug'),
                    'days' => env('OCPI_LOG_DAILY_DAYS', 60),
                ]
            );

        $this->loadRoutesFrom(__DIR__.'/Support/Server/Endpoints/common.php');
        $this->loadRoutesFrom(__DIR__.'/Support/Server/Endpoints/2.1.1.php');

        $this->loadMigrationsFrom(__DIR__.'/Data/Migrations');
    }
}
