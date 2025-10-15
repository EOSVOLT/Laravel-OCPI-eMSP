<?php

namespace Ocpi\Models\Sessions;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Enums\SessionStatus;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property string $session_id
 * @property int $party_role_id
 * @property PartyRole $party_role
 * @property int $location_id
 * @property Location $location
 * @property array $object
 * @property SessionStatus $status
 * @property int $location_evse_id
 * @property LocationEvse $location_evse
 */
class Session extends Model
{
    use HasUuids,
        SoftDeletes;

    protected $fillable = [
        'party_role_id',
        'location_id',
        'location_evse_id',
        'session_id',
        'object',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'object' => 'array',
            'status' => SessionStatus::class,
        ];
    }

    /***
     * Relations.
     ***/

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }public function location_evse(): BelongsTo
    {
        return $this->belongsTo(LocationEvse::class);
    }

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }
}
