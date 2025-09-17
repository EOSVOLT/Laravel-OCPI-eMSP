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
            $table->dropColumn('server_token');
            $table->dropColumn('client_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'parties', function (Blueprint $table) {
            $table->string('server_token')->nullable()->comment('Token when the Party acts as a Server / Receiver');
            $table->string('client_token')->nullable()->comment('Token when the Party acts as a Client / Sender');
        });
    }
};
