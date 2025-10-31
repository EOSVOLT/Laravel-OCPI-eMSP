<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Enums\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'party_roles', function (Blueprint $table) {
            $table->text('url')->nullable();
            $table->text('endpoints')->nullable();
        });
        Party::all()->each(function (Party $party) {
            $party->roles->each(function (PartyRole $partyRole) {
                return $partyRole->update([
                    'url' => $partyRole->party->url,
                    'endpoints' => $partyRole->party->endpoints,
                ]);
            });
        });
        Schema::table(config('ocpi.database.table.prefix') . 'parties', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->dropColumn('endpoints');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'parties', function (Blueprint $table) {
            $table->text('url')->nullable();
            $table->text('endpoints')->nullable();
        });
        Party::all()->each(function (Party $party) {
            $partyRole = $party->roles->where('role', Role::CPO)->first();
            $party->update([
                'url' => $partyRole?->url,
                'endpoints' => $partyRole?->endpoints,
            ]);
        });
        Schema::table(config('ocpi.database.table.prefix') . 'party_roles', function (Blueprint $table) {
            $table->text('url')->nullable();
            $table->text('endpoints')->nullable();
        });
    }
};
