<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyParty;

Route::middleware([
    IdentifyParty::class,
])
    ->prefix('tariffs')
    ->name('tariffs')
    ->group(function () {
        Route::get('', [GetController::class, 'list']);
        Route::put('/{country_code}/{party_id}/{external_id}', [PutController::class, 'upsert'])->name('.put');
    });
