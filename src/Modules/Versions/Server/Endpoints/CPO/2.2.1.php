<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Versions\Server\Controllers\CPO\DetailsController;

Route::name('versions.')
    ->group(function () {
        Route::get('/', DetailsController::class)
            ->name('details');
    });
