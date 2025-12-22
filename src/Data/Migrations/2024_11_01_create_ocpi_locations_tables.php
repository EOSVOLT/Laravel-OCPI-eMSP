<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return config('ocpi.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('ocpi.database.table.prefix').'locations', function (Blueprint $table) {
            $table->uuid('emsp_id')->primary();
            $table->foreignId('party_role_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'party_roles',
                    indexName: 'locations_party_role_id',
                )
                ->cascadeOnDelete();

            $table->string('id', length: 39);
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['party_role_id', 'id']);
            $table->index('id');
        });

        Schema::create(config('ocpi.database.table.prefix').'location_evses', function (Blueprint $table) {
            $table->uuid('emsp_id')->primary();
            $table->foreignUuid('location_emsp_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'locations',
                    column: 'emsp_id',
                )
                ->cascadeOnDelete();

            $table->string('uid', length: 39);
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['location_emsp_id', 'uid']);
            $table->index('uid');
        });

        Schema::create(config('ocpi.database.table.prefix').'location_connectors', function (Blueprint $table) {
            $table->uuid('emsp_id')->primary();
            $table->foreignUuid('location_evse_emsp_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'location_evses',
                    column: 'emsp_id',
                )
                ->cascadeOnDelete();

            $table->string('id', length: 36);
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['location_evse_emsp_id', 'id']);
            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix').'location_connectors');
        Schema::dropIfExists(config('ocpi.database.table.prefix').'location_evses');
        Schema::dropIfExists(config('ocpi.database.table.prefix').'locations');
    }
};
