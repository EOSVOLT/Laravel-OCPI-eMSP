<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Versions\Server\Controllers\EMSP\V2_2_1\DetailsController;

Route::name('versions.')
    ->group(function () {
        Route::get('/', DetailsController::class)
            ->name('details');
    });
