<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Sessions\Session;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('location_connector_id')->after('location_evse_id')->nullable();
            $table->foreign('location_connector_id')->references('id')->on(
                config('ocpi.database.table.prefix') . 'location_connectors'
            );
        });
        //migrate session data
        Session::all()->each(function (Session $session) {
            /** @var LocationConnector $locationConnector */
            $locationConnector = LocationConnector::query()
                ->whereHas('evse', function ($connectorQuery) use ($session) {
                    $connectorQuery->where('uid', $session->object['evse_uid']);
                })
                ->where('connector_id', $session->object['connector_id'])
                ->first();
            $session->location_connector_id = $locationConnector->id;
            $session->save();
        });
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('location_connector_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            if ('sqlite' !== DB::connection()->getDriverName()) {
                $table->dropForeign('location_connector_id');
            }
            $table->dropColumn('location_connector_id');
        });
    }
};
