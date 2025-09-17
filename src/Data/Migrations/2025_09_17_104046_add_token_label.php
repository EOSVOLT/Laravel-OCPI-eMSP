<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\PartyToken;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'party_tokens', function (Blueprint $table) {
            $table->string('name')->after('party_id');
        });
        PartyToken::all()->each(function (PartyToken $token) {
            $token->name = $token->party->name;
            $token->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'party_tokens', function (Blueprint $table) {
            $table->removeColumn('label');
        });
    }
};
