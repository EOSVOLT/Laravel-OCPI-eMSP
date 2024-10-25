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
    }
}
