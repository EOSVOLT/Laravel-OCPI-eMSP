<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property int $location_id
 * @property string $uid
 * @property AsArrayObject $object
 * @property EvseStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Location $location
 * @property Location $locationWithTrashed
 * @property LocationConnector[]|HasMany $connectorsWithTrashed
 * @property LocationConnector[]|HasMany $connectors
 */
class LocationEvse extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'location_id',
        'uid',
        'object',
        'status',
        'updated_at'
    ];

    protected function casts(): array
    {
        return [
            'object' => AsArrayObject::class,
            'status' => EvseStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /***
     * Relations.
     ***/

    public function connectors(): HasMany
    {
        return $this->hasMany(LocationConnector::class, 'evse_id', 'id');
    }

    public function connectorsWithTrashed(): HasMany
    {
        return $this->hasMany(LocationConnector::class, 'evse_id', 'id')
            ->withTrashed();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function locationWithTrashed(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id')
            ->withTrashed();
    }
}
