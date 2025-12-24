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
        DB::table(config('ocpi.database.table.prefix') . 'commands as c')
            ->update(['interface_role' => InterfaceRole::RECEIVER->value]);
        Schema::table(config('ocpi.database.table.prefix') . 'commands', function (Blueprint $table) {
            $table->enum('interface_role', InterfaceRole::stringCases())->change();
        });
    }

    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix'). 'commands', function (Blueprint $table) {
            $table->dropColumn('interface_role');
        });
    }
};
