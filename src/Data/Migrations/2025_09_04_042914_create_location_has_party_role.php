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
        Schema::disableForeignKeyConstraints();
        if ('sqlite' === DB::connection()->getDriverName()) {
            Schema::dropIfExists(config('ocpi.database.table.prefix') . 'locations');
            Schema::create(config('ocpi.database.table.prefix') . 'locations', function (Blueprint $table) {
                $table->uuid('emsp_id')->primary();
                $table->foreignId('party_id')
                    ->constrained(
                        table: config('ocpi.database.table.prefix').'parties',
                        indexName: 'locations_party_id',
                    )
                    ->cascadeOnDelete();

                $table->string('id', length: 39);
                $table->json('object');
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['party_id', 'id']);
                $table->index('id');
            });
        } else {
            Schema::table(config('ocpi.database.table.prefix') . 'locations', function (Blueprint $table) {
                $table->dropForeign('locations_party_role_id');//explicit custom foreign name
                $table->dropUnique(['party_role_id', 'id']);
                $table->dropColumn('party_role_id');
                $table->foreignId('party_id')->after('id')->constrained(
                    config('ocpi.database.table.prefix') . 'parties',
                    'id',
                    'locations_party_id'
                )->onDelete('cascade');
                $table->index(['emsp_id', 'party_id'],
                    config('ocpi.database.table.prefix') . 'locations_party_id_id_unique');
            });
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table(config('ocpi.database.table.prefix') . 'locations', function (Blueprint $table) {
            $table->foreignId('party_role_id')->after('emsp_id')->constrained(
                config('ocpi.database.table.prefix') . 'party_roles',
                'id',
                'locations_party_role_id'
            )->onDelete('cascade');
            $table->index(['emsp_id', 'party_role_id'],
                config('ocpi.database.table.prefix') . 'locations_party_role_id_id_unique');
        });
        Schema::enableForeignKeyConstraints();
    }
};
