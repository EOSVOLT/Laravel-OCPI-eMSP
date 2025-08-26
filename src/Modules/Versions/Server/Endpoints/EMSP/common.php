<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Versions\Server\Controllers\EMSP\InformationController;

Route::name('versions.')
    ->group(function () {
        Route::get('versions', InformationController::class)
            ->name('information');
    });
