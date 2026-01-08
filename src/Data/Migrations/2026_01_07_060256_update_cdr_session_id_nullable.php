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
            $table->string('session_id', 36)->nullable()->change();
            $table->text('external_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->string('session_id', 36)->nullable(false)->change();
            $table->dropColumn('external_url');
        });
    }
};
