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
        Schema::create(config('ocpi.database.table.prefix') . 'join_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_role_id')->constrained(config('ocpi.database.table.prefix') . 'party_roles');
            $table->foreignId('join_party_role_id')->constrained(config('ocpi.database.table.prefix') . 'party_roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix').'join_parties');
    }
};
