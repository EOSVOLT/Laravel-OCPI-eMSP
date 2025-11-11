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
        Schema::table(config('ocpi.database.table.prefix').'cdrs', function (Blueprint $table) {
            $table->dropIndex('ocpi_cdrs_id_index');
            $table->renameColumn('id', 'cdr_id');
            $table->index('cdr_id');
            $table->dropPrimary(['emsp_id']);
            $table->id()->after('emsp_id');
            $table->dropColumn('emsp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix').'cdrs', function (Blueprint $table) {
            $table->string('emsp_id', 36)->first();
            $table->dropColumn('id');
            $table->primary('emsp_id');

            $table->dropIndex('ocpi_cdrs_cdr_id_index');
            $table->renameColumn('cdr_id', 'id');
            $table->index('id');
        });
    }
};
