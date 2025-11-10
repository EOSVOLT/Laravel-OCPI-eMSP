<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix').'party_tokens', function (Blueprint $table) {
            $table->foreignId('party_role_id')->nullable();

            PartyRole::all()->each(function (PartyRole $partyRole) {
                $currentToken = $partyRole->party->tokens->first();
                $partyRole->tokens()->create([
                    'name' => $currentToken->name,
                    'token' => $currentToken->token,
                    'registered' => $currentToken->registered,
                    'deleted_at' => $currentToken->deleted_at
                ]);
                $currentToken->delete();
            });
        });
    }
};
