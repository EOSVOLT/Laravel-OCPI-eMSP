<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->dropForeign(['location_evse_emsp_id']);
            $table->dropColumn('location_evse_emsp_id');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->dropForeign(['location_evse_emsp_id']);
            $table->dropColumn('location_evse_emsp_id');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'location_connectors', function (Blueprint $table) {
            $table->dropForeign(['location_evse_emsp_id']);
            $table->dropUnique(['location_evse_emsp_id', 'id']);
            $table->dropColumn('location_evse_emsp_id');
            $table->renameColumn('id', 'connector_id');
            $table->dropPrimary('emsp_id');
            $table->id()->after('emsp_id');
            $table->dropColumn('emsp_id');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'location_evses', function (Blueprint $table) {
            $table->dropPrimary('emsp_id');
            $table->id()->after('emsp_id');
            $table->dropColumn('emsp_id');
            $table->dropForeign(['location_emsp_id']);
            $table->dropUnique(['location_emsp_id', 'uid']);
            $table->dropColumn('location_emsp_id');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'locations', function (Blueprint $table) {
            $table->renameColumn('id', 'external_id');
            $table->dropPrimary('emsp_id');
            $table->id()->after('emsp_id');
            $table->dropColumn('emsp_id');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'location_evses', function (Blueprint $table) {
            $table->foreignId('location_id')->after('id')->constrained('ocpi_locations');
            $table->unique(['location_id', 'uid'], 'location_id_uid_unique');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'location_connectors', function (Blueprint $table) {
            $table->foreignId('evse_id')->after('id')->constrained('ocpi_location_evses');
            $table->unique(['evse_id', 'connector_id'], 'evse_id_connector_id_unique');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'locations', function (Blueprint $table) {
            $table->boolean('publish')->after('object');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'location_evses', function (Blueprint $table) {
            $table->string('status')->after('object');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->foreignId('location_id')->after('party_role_id')->constrained(
                config('ocpi.database.table.prefix') . 'locations'
            )->restrictOnDelete();
        });
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->foreignId('location_id')->after('party_role_id')->constrained(
                config('ocpi.database.table.prefix') . 'locations'
            )->restrictOnDelete();
        });


        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn('location_id');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn('location_id');
        });
        // Reverse of the last operations first (drop new FKs/uniques/columns added in up)
        Schema::table(config('ocpi.database.table.prefix') . 'location_connectors', function (Blueprint $table) {
            $table->dropUnique('evse_id_connector_id_unique');
            $table->dropConstrainedForeignId('evse_id');
        });
        Schema::table(config('ocpi.database.table.prefix') . 'location_evses', function (Blueprint $table) {
            $table->dropUnique('location_id_uid_unique');
            $table->dropConstrainedForeignId('location_id');
        });

        // Revert ocpi_locations changes
        Schema::table('ocpi_locations', function (Blueprint $table) {
            $table->string('emsp_id', 36)->first();
            $table->dropColumn('id');
            $table->primary('emsp_id');
            $table->renameColumn('external_id', 'id');
        });

        // Revert ocpi_location_evses changes
        Schema::table(config('ocpi.database.table.prefix') . 'location_evses', function (Blueprint $table) {
            $table->string('emsp_id', 36)->first();
            $table->unsignedBigInteger('location_emsp_id')->after('emsp_id');
            $table->dropColumn('id');
            $table->primary('emsp_id');
            $table->unique(['location_emsp_id', 'uid'], 'ocpi_location_evses_location_emsp_id_uid_unique');
            $table->foreign('location_emsp_id', 'ocpi_location_evses_location_emsp_id_foreign')
                ->references('emsp_id')->on('ocpi_locations')
                ->onDelete('cascade');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'location_connectors', function (Blueprint $table) {
            $table->string('emsp_id', 36)->first();
            $table->unsignedBigInteger('location_evse_emsp_id')->after('emsp_id');
            $table->dropColumn('id');
            $table->renameColumn('connector_id', 'id');
            $table->primary('emsp_id');
            $table->unique(['location_evse_emsp_id', 'id'], 'ocpi_location_connectors_location_evse_emsp_id_id_unique');
            $table->foreign('location_evse_emsp_id', 'ocpi_location_connectors_location_evse_emsp_id_foreign')
                ->references('emsp_id')->on('ocpi_location_evses')
                ->onDelete('cascade');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'locations', function (Blueprint $table) {
            $table->dropColumn('publish');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'location_evses', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'cdrs', function (Blueprint $table) {
            $table->foreignId('location_evse_emsp_id')->after('party_role_id')->constrained(
                config('ocpi.database.table.prefix') . 'locations'
            )->restrictOnDelete();
        });
        Schema::table(config('ocpi.database.table.prefix') . 'sessions', function (Blueprint $table) {
            $table->foreignId('location_evse_emsp_id')->after('party_role_id')->constrained(
                config('ocpi.database.table.prefix') . 'locations'
            )->restrictOnDelete();
        });
        Schema::enableForeignKeyConstraints();
    }
};
