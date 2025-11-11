<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::enableForeignKeyConstraints();
        Schema::table(config('ocpi.database.table.prefix') . "sessions", function (Blueprint $table) {
            $table->dropIndex('ocpi_sessions_id_index');
            $table->dropForeign(['party_role_id']);
            $table->dropUnique(['party_role_id', 'id']);
            $table->dropColumn('id');
            $table->string('session_id', 36)->after('party_role_id');
            $table->index('session_id');
            $table->unique(['party_role_id', 'session_id']);
            $table->foreign('party_role_id')
                ->references('id')
                ->on(config('ocpi.database.table.prefix') . "party_roles")
                ->onDelete('restrict');

            $table->dropPrimary();
            $table->renameColumn('emsp_id', 'id');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . "sessions", function (Blueprint $table) {
            $table->dropPrimary();
            $table->renameColumn('id', 'emsp_id');
            $table->primary('emsp_id');

            $table->dropIndex('ocpi_sessions_session_id_index');
            $table->dropForeign(['party_role_id']);
            $table->dropUnique(['party_role_id', 'session_id']);
            $table->dropColumn('session_id');
            $table->string('id', 36)->after('party_role_id');
            $table->index('id');
            $table->unique(['party_role_id', 'id']);
            $table->foreign('party_role_id','sessions_party_role_id')
                ->references('id')
                ->on(config('ocpi.database.table.prefix') . "party_roles")
                ->onDelete('cascade');
        });
    }
};
