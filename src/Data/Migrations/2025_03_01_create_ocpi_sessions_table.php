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
        Schema::create(config('ocpi.database.table.prefix').'sessions', function (Blueprint $table) {
            $table->uuid('emsp_id')->primary();
            $table->foreignId('party_role_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'party_roles',
                    indexName: 'sessions_party_role_id',
                )
                ->cascadeOnDelete();
            $table->foreignUuid('location_evse_emsp_id')
                ->nullable()
                ->constrained(
                    table: config('ocpi.database.table.prefix').'location_evses',
                    column: 'emsp_id',
                )
                ->cascadeOnDelete();

            $table->string('id', length: 36);
            $table->json('object');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['party_role_id', 'id']);
            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix').'sessions');
    }
};
