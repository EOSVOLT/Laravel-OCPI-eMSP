<?php

namespace Ocpi\Models\Locations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Ocpi\Models\Tariffs\Tariff;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property int $evse_id
 * @property LocationEvse $evse
 * @property array $object
 * @property int $connector_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Tariff[]|Collection $tariffs
 */
class LocationConnector extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'evse_id',
        'connector_id',
        'object',
    ];

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

    protected function casts(): array
    {
        return [
            'object' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
