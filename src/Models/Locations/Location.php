<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Models\Model;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'object',
        'party_id',
        'external_id',
        'publish'
    ];

    protected function casts(): array
    {
        return [
            'object' => AsArrayObject::class,
        ];
    }

    /***
     * Scopes.
     ***/

    public function scopePartyRole(Builder $query, int $party_role_id): void
    {
        $query->where('party_role_id', $party_role_id);
    }

    /***
     * Relations.
     ***/

    public function evses(): HasMany
    {
        return $this->hasMany(LocationEvse::class, 'evse_id', 'id');
    }

    public function evsesWithTrashed(): HasMany
    {
        return $this->hasMany(LocationEvse::class, 'evse_id', 'id')
            ->withTrashed();
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class, 'party_id', 'id');
    }
}
