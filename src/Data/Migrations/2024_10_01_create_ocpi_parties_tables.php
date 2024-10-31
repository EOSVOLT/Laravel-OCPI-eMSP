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
        Schema::create(config('ocpi.database.table.prefix').'parties', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('server_token')->nullable()->comment('Token when the Party acts as a Server / Receiver');
            $table->string('url')->nullable()->comment('OCPI Versions Information endpoint');
            $table->string('version', 10)->nullable()->comment('Mutual OCPI version');
            $table->string('version_url')->nullable()->comment('OCPI Versions Details endpoint');
            $table->json('endpoints')->nullable()->comment('Supported OCPI endpoints for the version');
            $table->string('client_token')->nullable()->comment('Token when the Party acts as a Client / Sender');
            $table->boolean('registered')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('ocpi.database.table.prefix').'party_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')
                ->constrained(
                    table: config('ocpi.database.table.prefix').'parties',
                    indexName: 'party_roles_party_id',
                )
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('code', 3)->comment('party_id: CPO, eMSP, ... ID of the party (following the ISO-15118 standard)');
            $table->string('role', 10);
            $table->string('country_code', 2);
            $table->json('business_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix').'party_roles');
        Schema::dropIfExists(config('ocpi.database.table.prefix').'parties');
    }
};
