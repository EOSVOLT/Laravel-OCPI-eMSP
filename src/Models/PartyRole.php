<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $party_id
 * @property Party $party
 * @property Role $role
 * @property string $country_code
 * @property AsArrayObject|null $business_details
 * @property string|null $url
 * @property string|null $endpoints
 * @property PartyToken[]|Collection $tokens
 */
class PartyRole extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'role',
        'country_code',
        'business_details',
        'url',
        'endpoints',
    ];

    protected function casts(): array
    {
        return [
            'business_details' => AsArrayObject::class,
            'role' => Role::class,
        ];
    }

    /***
     * Scopes.
     ***/

    public function scopeCode(Builder $query, string $code): void
    {
        $query->where('code', $code);
    }

    public function scopeCountryCode(Builder $query, string $countryCode): void
    {
        $query->where('country_code', $countryCode);
    }

    /***
     * Relations.
     ***/

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(PartyToken::class, 'party_role_id');
    }
}
