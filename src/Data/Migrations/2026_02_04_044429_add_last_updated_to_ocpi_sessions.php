<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Sessions\Session;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->timestamp('last_updated')->nullable();
        });
        Session::all()->each(function (Session $session) {
            $lastUpdated = $session->object['last_updated'] ?? null;
            if (null !== $lastUpdated) {
                $date = Carbon\Carbon::parse($lastUpdated);
                $session->update([
                    'last_updated' => $date,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->dropColumn('last_updated');
        });
    }
};
