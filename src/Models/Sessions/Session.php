<?php

namespace Ocpi\Models\Sessions;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Database\Factories\SessionFactory;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationConnector;
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
 * @property LocationEvse $evse
 * @property int $location_connector_id
 * @property LocationConnector $connector
 */
class Session extends Model
{
    use HasUuids;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'party_role_id',
        'location_id',
        'location_evse_id',
        'location_connector_id',
        'session_id',
        'object',
        'status',
    ];

    protected static function newFactory(): SessionFactory
    {
        return SessionFactory::new();
    }

    /***
     * Relations.
     ***/

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function evse(): BelongsTo
    {
        return $this->belongsTo(LocationEvse::class, 'location_evse_id', 'id');
    }

    public function connector(): BelongsTo
    {
        return $this->belongsTo(LocationConnector::class, 'location_connector_id', 'id');
    }

    public function location_evse(): BelongsTo
    {
        return $this->belongsTo(LocationEvse::class);
    }

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }

    protected function casts(): array
    {
        return [
            'object' => 'array',
            'status' => SessionStatus::class,
        ];
    }
}
