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
        Schema::create(config('ocpi.database.table.prefix').'commands', function (Blueprint $table) {
            $table->foreignId('party_role_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'party_roles',
                    indexName: 'commands_party_role_id',
                )
                ->cascadeOnDelete();
            $table->ulid('id')->primary();
            $table->string('type', length: 20);
            $table->json('payload')->nullable();
            $table->string('response', length: 15)->nullable();
            $table->string('result', length: 20)->nullable();
            $table->timestamps();

            $table->index('response');
            $table->index('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix').'commands');
    }
};
