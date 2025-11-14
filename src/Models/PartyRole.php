<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Ocpi\Database\Factories\PartyRoleFactory;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property PartyRole|null $parent_role
 * @property int|null $parent_role_id
 * @property Collection|PartyRole[] $children_role
 * @property string $code
 * @property string $party_id
 * @property Party $party
 * @property Role $role
 * @property string $country_code
 * @property array|null $business_details
 * @property string|null $url
 * @property string|null $endpoints
 * @property PartyToken[]|Collection $tokens
 */
class PartyRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'party_id',
        'parent_role_id',
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
            'business_details' => 'array',
            'endpoints' => 'array',
            'role' => Role::class,
        ];
    }

    protected static function newFactory(): PartyRoleFactory
    {
        return PartyRoleFactory::new();
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

    public function parent_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class, 'parent_role_id');
    }

    public function children_role(): HasMany
    {
        return $this->hasMany(PartyRole::class, 'parent_role_id');
    }
}
