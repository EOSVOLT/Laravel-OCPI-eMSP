<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ocpi\Modules\Commands\Enums\CommandType;
use Ocpi\Support\Enums\InterfaceRole;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'commands', function (Blueprint $table) {
            $table->foreignId('command_token_id')->nullable()->constrained(
                config('ocpi.database.table.prefix') . 'command_token'
            )->cascadeOnDelete();
            $table->enum('type', InterfaceRole::cases())->nullable();
        });
        DB::table(config('ocpi.database.table.prefix' . 'commands'))
            ->update(['type' => InterfaceRole::RECEIVER->value]);

        DB::table(config('ocpi.database.table.prefix' . 'commands'))
            ->where('type', CommandType::START_SESSION)
            ->chunk(100, function ($commands) {
                foreach ($commands as $command) {
                    $payload = is_string($command->payload)
                        ? json_decode($command->payload, true)
                        : (array)$command->payload;
                    $uid = $payload['uid'] ?? null;
                    if (null === $uid) {
                        continue;
                    }
                    $result = DB::table(config('ocpi.database.table.prefix' . 'command_token'))->updateOrInsert(
                        ['uid' => $uid, 'party_role_id' => $command->party_role_id],
                        [
                            'type' => $payload['type'],
                            'visual_number' => $payload['visual_number'] ?? null,
                            'group_id' => $payload['group_id'] ?? null,
                            'contract_id' => $payload['contract_id'] ?? null,
                            'issuer' => $payload['issuer'],
                            'valid' => $payload['valid'],
                            'whitelist_type' => $payload['whitelist'],
                            'language' => $payload['language'] ?? null,
                            'default_profile_type' => $payload['default_profile_type'] ?? null,
                            'energy_contract' => $payload['energy_contract'] ?? null,
                            'created_at' => $payload['last_updated'] ?? now(),
                            'updated_at' => $payload['last_updated'] ?? now(),
                        ]
                    );
                    if (false === $result) {
                        continue;
                    }
                    $commandToken = DB::table(config('ocpi.database.table.prefix' . 'command_token'))
                        ->where('uid', $uid)
                        ->where('party_role_id', $command->party_role_id)
                        ->first();
                    if (null === $commandToken) {
                        continue;
                    }
                    DB::table(config('ocpi.database.table.prefix' . 'commands'))->update(
                        ['command_token_id' => $commandToken->id]
                    );
                }
            });
    }
};
