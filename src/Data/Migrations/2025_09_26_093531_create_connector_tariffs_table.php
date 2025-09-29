<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            config('ocpi.database.table.prefix') . 'location_connector_tariffs',
            function (Blueprint $table) {
                $table->foreignId('connector_id')->constrained(
                    config('ocpi.database.table.prefix') . 'location_connectors',
                    indexName: 'location_connector_tariffs_connector_id',
                )->onDelete('cascade');
                $table->foreignId('tariff_id')->constrained(
                    config('ocpi.database.table.prefix') . 'tariffs',
                    indexName: 'location_connector_tariffs_tariff_id',
                )->onDelete(
                    'cascade'
                );
                $table->unique(['connector_id', 'tariff_id']);
            }
        );
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'location_connector_tariffs');
        Schema::enableForeignKeyConstraints();
    }
};
