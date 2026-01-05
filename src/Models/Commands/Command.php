<?php

namespace Ocpi\Models\Commands;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Tokens\CommandToken;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Modules\Commands\Enums\CommandResultType;
use Ocpi\Modules\Commands\Enums\CommandType;
use Ocpi\Support\Enums\InterfaceRole;
use Ocpi\Support\Models\Model;

/**
 * @property PartyRole $party_role
 * @property int $party_role_id
 * @property string $id
 * @property CommandType $type
 * @property array|null $payload
 * @property CommandResponseType|null $response
 * @property CommandResultType|null $result
 * @property CommandToken|null $command_token
 * @property int|null $command_token_id
 * @property InterfaceRole $interface_role
 */
class Command extends Model
{
    use HasUlids;
    use HasFactory;

    protected $fillable = [
        'party_role_id',
        'id',
        'type',
        'payload',
        'response',
        'result',
        'command_token_id',
        'interface_role',
    ];

    protected function casts(): array
    {
        return [
            'type' => CommandType::class,
            'payload' => 'array',
            'response' => CommandResponseType::class,
            'result' => CommandResultType::class,
            'interface_role' => InterfaceRole::class,
        ];
    }

    /***
     * Relations.
     ***/

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }

    public function command_token(): BelongsTo
    {
        return $this->belongsTo(CommandToken::class);
    }
}
