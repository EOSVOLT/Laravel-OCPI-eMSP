<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('ocpi.database.table.prefix') . 'command_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_role_id')->constrained(
                config('ocpi.database.table.prefix') . 'party_roles'
            )->cascadeOnDelete();
            $table->string('uid', 36);
            $table->string('type');
            $table->string('visual_number', 64)->nullable();
            $table->string('group_id', 36)->nullable();
            $table->string('contract_id', 36);
            $table->string('issuer', 64);
            $table->boolean('valid');
            $table->string('whitelist_type');
            $table->string('language', 2)->nullable();
            $table->string('default_profile_type')->nullable();
            $table->json('energy_contract')->nullable();
            $table->timestamps();

            $table->unique(['party_role_id', 'uid', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'command_tokens');
    }
};
