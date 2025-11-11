<?php

namespace Ocpi\Models;

use Database\Factories\PartyRoleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $party_id
 * @property Party $party
 * @property Role $role
 * @property string $country_code
 * @property array|null $business_details
 */
class PartyRole extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'code',
        'role',
        'country_code',
        'business_details',
    ];

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

    protected function casts(): array
    {
        return [
            'business_details' => 'array',
            'role' => Role::class,
        ];
    }
}
