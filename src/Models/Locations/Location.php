<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Ocpi\Models\Party;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Support\Models\Model;

/**
 * @property Party $party
 * @property AsArrayObject $object
 * @property int $party_id
 * @property string $external_id
 * @property bool $publish
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property int $id
 */
class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'object',
        'party_id',
        'external_id',
        'publish',
        'updated_at',
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
     * Scopes.
     ***/

    #[Scope]
    public function partyRole(Builder $query, int $party_role_id): void
    {
        $query->where('party_role_id', $party_role_id);
    }

    #[Scope]
    public function withHasValidEvses(Builder $query): void
    {
        $query->with('evses', function (HasMany $query) {
            $query->with('connectors')
                ->whereNotIn('status', [EvseStatus::REMOVED, EvseStatus::UNKNOWN]);
        })->whereHas('evses', function ($query) {
            $query->whereNotIn('status', [EvseStatus::REMOVED, EvseStatus::UNKNOWN]);
        });
    }

    /***
     * Relations.
     ***/

    public function evses(): HasMany
    {
        return $this->hasMany(LocationEvse::class, 'location_id', 'id');
    }

    public function evsesWithTrashed(): HasMany
    {
        return $this->hasMany(LocationEvse::class, 'location_id', 'id')
            ->withTrashed();
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id', 'id');
    }


}
