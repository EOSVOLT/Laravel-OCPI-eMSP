<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ('sqlite' === $this->connection->getDriverName()) {
            Schema::dropIfExists(config('ocpi.database.table.prefix').'cdrs');
            Schema::create(config('ocpi.database.table.prefix').'cdrs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('party_role_id')
                    ->constrained(
                        table: config('ocpi.database.table.prefix').'party_roles',
                        indexName: 'cdrs_party_role_id',
                    )
                    ->cascadeOnDelete();
                $table->foreignId('location_id')->after('party_role_id')->constrained(
                    config('ocpi.database.table.prefix') . 'locations'
                )->restrictOnDelete();

                $table->string('id', length: 39);
                $table->json('object');
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['party_role_id', 'id']);
                $table->index('id');
            });
        }else{
            Schema::table(config('ocpi.database.table.prefix').'cdrs', function (Blueprint $table) {
                $table->dropIndex('ocpi_cdrs_id_index');
                $table->renameColumn('id', 'cdr_id');
                $table->index('cdr_id');
                $table->dropPrimary(['emsp_id']);
                $table->id()->after('emsp_id');
                $table->dropColumn('emsp_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix').'cdrs', function (Blueprint $table) {
            $table->string('emsp_id', 36)->first();
            $table->dropColumn('id');
            $table->primary('emsp_id');

            $table->dropIndex('ocpi_cdrs_cdr_id_index');
            $table->renameColumn('cdr_id', 'id');
            $table->index('id');
        });
    }
};
