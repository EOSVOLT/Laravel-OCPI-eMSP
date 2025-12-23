<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Ocpi\Database\Factories\LocationEvseFactory;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property int $location_id
 * @property string $uid
 * @property array $object
 * @property EvseStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Location $location
 * @property Location $locationWithTrashed
 * @property LocationConnector[]|Collection $connectorsWithTrashed
 * @property LocationConnector[]|Collection $connectors
 */
class LocationEvse extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'location_id',
        'uid',
        'object',
        'status',
        'updated_at',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'object' => 'array',
            'status' => EvseStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function newFactory(): LocationEvseFactory
    {
        return LocationEvseFactory::new();
    }
    /***
     * Scopes.
     ***/

    #[Scope]
    public function validEvse(Builder $query): void
    {
        $query->whereNotIn('status', [EvseStatus::REMOVED, EvseStatus::UNKNOWN]);
    }

    /***
     * Relations.
     ***/

    public function connectors(): HasMany
    {
        return $this->hasMany(LocationConnector::class, 'evse_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function connectorsWithTrashed(): HasMany
    {
        return $this->hasMany(LocationConnector::class, 'evse_id', 'id')
            ->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function locationWithTrashed(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id')
            ->withTrashed();
    }
}
