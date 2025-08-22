<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Versions\Server\Controllers\DetailsController;

Route::name('versions.')
    ->group(function () {
        Route::get('/', DetailsController::class)
            ->name('details');
    });
