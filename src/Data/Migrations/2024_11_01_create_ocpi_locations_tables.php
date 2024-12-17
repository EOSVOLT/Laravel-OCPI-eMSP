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
            $table->string('id', length: 39);
            $table->foreignId('party_role_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'party_roles',
                    indexName: 'locations_party_role_id',
                )
                ->cascadeOnDelete();
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['id', 'party_role_id']);
            $table->index('id');
        });

        Schema::create(config('ocpi.database.table.prefix').'location_evses', function (Blueprint $table) {
            $table->string('composite_id', length: 79);
            $table->string('uid', length: 39);
            $table->string('location_id', length: 39);
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['location_id', 'uid']);
            $table->unique(['composite_id']);
            $table->index('uid');

            $table->foreign('location_id', name: 'location_evses_location_id')
                ->references('id')->on(config('ocpi.database.table.prefix').'locations')
                ->cascadeOnDelete();
        });

        Schema::create(config('ocpi.database.table.prefix').'location_connectors', function (Blueprint $table) {
            $table->string('id', length: 36);
            $table->string('location_evse_composite_id', length: 79);
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['location_evse_composite_id', 'id']);

            $table->foreign('location_evse_composite_id')
                ->references('composite_id')->on(config('ocpi.database.table.prefix').'location_evses')
                ->cascadeOnDelete();
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
