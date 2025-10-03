<?php

namespace Ocpi\Models\Locations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property int $evse_id
 * @property LocationEvse $evse
 * @property AsArrayObject $object
 * @property int $connector_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class LocationConnector extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'evse_id',
        'connector_id',
        'object',
    ];

    protected function casts(): array
    {
        return [
            'object' => AsArrayObject::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /***
     * Relations.
     ***/

    public function evse(): BelongsTo
    {
        return $this->belongsTo(LocationEvse::class, 'evse_id', 'id');
    }

    /**
     * @return HasManyThrough
     */
    public function tariffs(): HasManyThrough
    {
        return $this->hasManyThrough(
            Tariff::class,
            LocationConnectorTariff::class,
            'location_connector_id',
            'id',
            'id',
            'tariff_id'
        );
    }
}
