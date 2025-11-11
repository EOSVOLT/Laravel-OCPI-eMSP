<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Models\Sessions\Session;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('location_evse_id')->after('location_id')->nullable();
            $table->foreign('location_evse_id')->references('id')->on(
                config('ocpi.database.table.prefix') . 'location_evses'
            );
        });
        //migrate session data
        Session::all()->each(function (Session $session) {
            /** @var LocationEvse $evse */
            $evse = LocationEvse::query()->where('uid', $session->object['evse_uid'])->first();
            $session->location_evse_id = $evse->id;
            $session->save();
        });
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('location_evse_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            if ('sqlite' !== DB::connection()->getDriverName()) {
                $table->dropForeign('location_evse_id');
            }
            $table->dropColumn('location_evse_id');
        });
    }
};
