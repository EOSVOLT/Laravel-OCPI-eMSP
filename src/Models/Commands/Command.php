<?php

namespace Ocpi\Models\Commands;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Modules\Commands\Enums\CommandResultType;
use Ocpi\Modules\Commands\Enums\CommandType;
use Ocpi\Support\Models\Model;

/**
 * @property PartyRole $party_role
 * @property int $party_role_id
 * @property string $id
 * @property CommandType $type
 * @property array|null $payload
 * @property CommandResponseType|null $response
 * @property CommandResultType|null $result
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
    ];

    protected function casts(): array
    {
        return [
            'type' => CommandType::class,
            'payload' => 'array',
            'response' => CommandResponseType::class,
            'result' => CommandResultType::class,
        ];
    }

    /***
     * Relations.
     ***/

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }
}
