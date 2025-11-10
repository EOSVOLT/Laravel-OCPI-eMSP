<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn(config('ocpi.database.table.prefix').'party_tokens', 'party_role_id')) {
            Schema::table(config('ocpi.database.table.prefix').'party_tokens', function (Blueprint $table) {
                $table->foreignId('party_role_id')->nullable()->after('party_id')->constrained(config('ocpi.database.table.prefix').'party_roles');
            });
        }
        PartyRole::all()->each(function (PartyRole $partyRole) {
            $currentToken = $partyRole->party->tokens->first();
            $partyRole->tokens()->create([
                'party_id' => $currentToken->party_id,
                'name' => $currentToken->name,
                'token' => $currentToken->token,
                'registered' => $currentToken->registered,
                'deleted_at' => $currentToken->deleted_at
            ]);
            $currentToken->delete();
        });
    }

    public function down(): void
    {
        PartyRole::all()->each(function (PartyRole $partyRole) {
            $partyRole->tokens()->get()->each(function (PartyToken $token) {
                $token->create([
                    'party_id' => $token->party_id,
                    'name' => $token->name,
                    'token' => $token->token,
                    'registered' => $token->registered,
                    'deleted_at' => $token->deleted_at,
                    'party_role_id' => null,
                ]);
                $token->delete();
            });
        });
    }
};
