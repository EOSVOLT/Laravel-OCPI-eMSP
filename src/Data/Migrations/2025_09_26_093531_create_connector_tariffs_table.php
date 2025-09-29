<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('ocpi.database.table.prefix').'location_connector_tariffs', function (Blueprint $table) {
            $table->foreign('connector_id')->references('id')->on(config('ocpi.database.table.prefix') . 'location_connectors');
            $table->foreign('tariff_id')->references('id')->on(config('ocpi.database.table.prefix') . 'tariffs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connector_tariffs');
    }
};
