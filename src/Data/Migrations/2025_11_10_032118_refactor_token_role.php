<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Enums\Role;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table(config('ocpi.database.table.prefix') . 'party_tokens', function (Blueprint $table) {
            $table->foreignId('party_role_id')->nullable()->after('id')->constrained(
                config('ocpi.database.table.prefix') . 'party_roles'
            )->cascadeOnDelete();
        });
        Schema::table(config('ocpi.database.table.prefix') . 'party_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_role_id')->nullable()->after('id');
        });
        Party::all()->each(function (Party $party) {
            $currentTokens = DB::table(config('ocpi.database.table.prefix') . 'party_tokens')
                ->where('party_id', $party->id)
                ->whereNull('party_role_id')
                ->get();
            if (!$currentTokens) {
                return;
            }
            $party->roles()->each(function (PartyRole $partyRole) use ($party, $currentTokens) {
                if (null !== $party->parent_id) {
                    $partyRole->update(['parent_role_id' => $party->parent->roles()->where('role', Role::CPO)->first()?->id]);
                }
                foreach ($currentTokens as $token) {
                    PartyToken::insert([
                        'party_id' => $party->id,
                        'party_role_id' => $partyRole->id,
                        'name' => $token->name,
                        'token' => $token->token,
                        'registered' => $token->registered,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            });
            $currentTokens->each(function ($token) {
                PartyToken::find($token->id)?->forceDelete();
            });
        });
        Schema::table(config('ocpi.database.table.prefix') . 'party_tokens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('party_id');
            $table->foreignId('party_role_id')->nullable(false)->change();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table(config('ocpi.database.table.prefix') . 'party_tokens', function (Blueprint $table) {
            $table->foreignId('party_id')->nullable()->after('id')->constrained(
                config('ocpi.database.table.prefix') . 'parties'
            )->cascadeOnDelete();
        });
        PartyRole::all()->each(function (PartyRole $partyRole) {
            PartyToken::where('party_role_id', $partyRole->id)->whereNull('party_id')->get()->each(
                function (PartyToken $token) use ($partyRole) {
                    $check = PartyToken::where('party_id', $partyRole->party_id)->where('token', $token->token)->first();
                    if (null === $check) {
                        PartyToken::insert([
                            'party_id' => $partyRole->party_id,
                            'token' => $token->token,
                            'party_role_id' => $partyRole->id,
                            'name' => $token->name,
                            'registered' => $token->registered,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                        return;
                    }
                    $check->update([
                        'party_role_id' => $partyRole->id,
                        'name' => $token->name,
                        'registered' => $token->registered,
                    ]);
                    $token->forceDelete();
                }
            );
        });
        Schema::table(config('ocpi.database.table.prefix') . 'party_tokens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('party_role_id');
            $table->foreignId('party_id')->nullable(false)->change();
        });
        Schema::table(config('ocpi.database.table.prefix') . 'party_roles', function (Blueprint $table) {
            $table->dropColumn('parent_role_id');
        });
        Schema::enableForeignKeyConstraints();
    }
};
