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
        Schema::table(config('ocpi.database.table.prefix') . 'parties', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('registered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'parties', function (Blueprint $table) {
            $table->string('name');
            $table->boolean('registered')->default(false);
        });
    }
};
