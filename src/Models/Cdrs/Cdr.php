<?php

namespace Ocpi\Models\Cdrs;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Sessions\Session;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property PartyRole $party_role
 * @property int $party_role_id
 * @property string $cdr_id
 * @property array $object
 * @property Location $location
 * @property int $location_id
 * @property int $location_evse_id
 * @property LocationEvse $location_evse
 * @property Session $session
 * @property string $session_id
 */
class Cdr extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'party_role_id',
        'location_id',
        'location_evse_id',
        'session_id',
        'cdr_id',
        'object',
    ];

    /***
     * Relations.
     ***/

    public function location_evse(): BelongsTo
    {
        return $this->belongsTo(LocationEvse::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }

    protected function casts(): array
    {
        return [
            'object' => AsArrayObject::class,
        ];
    }
}
