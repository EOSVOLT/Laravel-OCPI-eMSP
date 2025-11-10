<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Versions\Server\Controllers\CPO\V2_2_1\InformationController;

Route::name('versions.')
    ->group(function () {
        Route::get('versions', InformationController::class)
            ->name('information');
    });
