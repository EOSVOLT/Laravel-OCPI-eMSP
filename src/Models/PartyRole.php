<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Ocpi\Database\Factories\PartyRoleFactory;
use Ocpi\Models\Tokens\CommandToken;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property PartyRole|null $parent_role
 * @property int|null $parent_role_id
 * @property Collection|PartyRole[] $children_roles
 * @property string $code
 * @property int $party_id
 * @property Party $party
 * @property Role $role
 * @property string $country_code
 * @property array|null $business_details
 * @property string|null $url
 * @property string|null $endpoints
 * @property PartyToken[]|Collection $tokens
 * @property CommandToken[]|Collection $command_tokens
 * @property PartyRole[]|Collection $join_party_roles
 */
class PartyRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var string[]
     */
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

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (PartyRole $partyRole) {
            // Soft delete all related tokens
            $partyRole->tokens()->delete();
        });
    }

    /**
     * @return PartyRoleFactory
     */
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

    /**
     * @param Builder $query
     * @param string $countryCode
     * @return void
     */
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

    /**
     * @return HasMany
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(PartyToken::class, 'party_role_id');
    }

    /**
     * @return BelongsTo
     */
    public function parent_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class, 'parent_role_id');
    }

    /**
     * @return HasMany
     */
    public function children_roles(): HasMany
    {
        return $this->hasMany(PartyRole::class, 'parent_role_id');
    }

    /**
     * @return HasMany
     */
    public function command_tokens(): HasMany
    {
        return $this->hasMany(
            CommandToken::class,
            'party_role_id',
            'id',
        );
    }

    /**
     * @return BelongsToMany
     */
    public function join_party_roles(): BelongsToMany
    {
        return $this->belongsToMany(PartyRole::class, JoinParty::class, 'party_role_id');
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'business_details' => 'array',
            'endpoints' => 'array',
            'role' => Role::class,
        ];
    }
}
