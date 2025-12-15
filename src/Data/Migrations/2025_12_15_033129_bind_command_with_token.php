<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ocpi\Support\Enums\InterfaceRole;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'commands', function (Blueprint $table) {
            $table->enum('interface_role', InterfaceRole::cases())->nullable();
        });
        DB::table(config('ocpi.database.table.prefix' . 'commands'))
            ->join(config('ocpi.database.table.prefix') .'party_roles as pr', 'commands.party_role_id', '=', 'pr.id')
            ->update(['interface_role' => InterfaceRole::RECEIVER->value]);
        Schema::table(config('ocpi.database.table.prefix') . 'commands', function (Blueprint $table) {
            $table->enum('interface_role', InterfaceRole::cases())->change();
        });
    }
};
