<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ocpi_locations', function (Blueprint $table) {
            $table->renameColumn('id', 'external_id');
            $table->renameColumn('emsp_id', 'id');
        });
        Schema::table('ocpi_location_evses', function (Blueprint $table) {
            $table->renameColumn('emsp_id', 'id');
            $table->renameColumn('location_emsp_id', 'location_id');
        });
        Schema::table('ocpi_location_connectors', function (Blueprint $table) {
            $table->renameColumn('id', 'connector_id');
            $table->renameColumn('emsp_id', 'id');
            $table->renameColumn('location_evse_emsp_id', 'evse_id');
        });
    }

    public function down(): void
    {
        Schema::table('ocpi_location_connectors', function (Blueprint $table) {
            $table->renameColumn('id', 'emsp_id');
            $table->renameColumn('connector_id', 'id');
            $table->renameColumn('evse_id', 'location_evse_emsp_id');
        });
        Schema::table('ocpi_location_evses', function (Blueprint $table) {
            $table->renameColumn('id', 'emsp_id');
            $table->renameColumn('location_id', 'location_emsp_id');
        });
        Schema::table('ocpi_locations', function (Blueprint $table) {
            $table->renameColumn('id', 'emsp_id');
            $table->renameColumn('external_id', 'id');
        });
    }
};
