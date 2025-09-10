<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ocpi_locations', function (Blueprint $table) {
            $table->renameColumn('id', 'external_id');
            $table->removeColumn('emsp_id');
            $table->id();
        });
        Schema::table('ocpi_location_evses', function (Blueprint $table) {
            $table->removeColumn('emsp_id');
            $table->id();
            $table->removeColumn('location_emsp_id');
            $table->foreignId('location_id')->constrained('ocpi_locations');
        });
        Schema::table('ocpi_location_connectors', function (Blueprint $table) {
            $table->removeColumn('emsp_id');
            $table->id();
            $table->removeColumn('location_evse_emsp_id');
            $table->foreignId('evse_id')->constrained('ocpi_location_evses');
        });
    }

    public function down(): void
    {

    }
};
