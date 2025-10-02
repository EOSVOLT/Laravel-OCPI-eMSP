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
        Schema::table(config('ocpi.database.table.prefix').'sessions', function (Blueprint $table) {
            $table->string('invoker')->after('session_id');
            $table->string('status')->after('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix').'sessions', function (Blueprint $table) {
            $table->dropColumn('invoker');
            $table->dropColumn('status');
        });
    }
};
