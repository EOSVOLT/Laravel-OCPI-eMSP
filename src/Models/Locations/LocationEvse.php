<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Support\Models\Model;

class LocationEvse extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'location_id',
        'uid',
        'object',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'object' => AsArrayObject::class,
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
