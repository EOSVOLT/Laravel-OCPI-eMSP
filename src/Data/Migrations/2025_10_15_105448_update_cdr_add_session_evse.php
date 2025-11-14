<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->unsignedBigInteger('location_evse_id')->after('location_id');
            $table->foreign('location_evse_id')->references('id')->on(
                config('ocpi.database.table.prefix') . 'location_evses'
            );
            $table->string('session_id', 36)->after('location_evse_id');
            $table->foreign('session_id')->references('id')->on(config('ocpi.database.table.prefix') . 'sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->dropForeign(['location_evse_id']);
            $table->removeColumn('location_evse_id');
            $table->dropForeign(['session_id']);
            $table->removeColumn('session_id');
        });
    }
};
