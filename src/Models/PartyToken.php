<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property Party $party
 * @property int $party_role_id
 * @property PartyRole $party_role
 * @property string $name
 * @property string $token
 * @property bool $registered
 */
class PartyToken extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'party_role_id',
        'name',
        'token',
        'registered',
    ];

    protected $attributes = [
        'registered' => false,
    ];

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }

    public function registered(Builder $query): Builder
    {
        return $query->where('registered', true);
    }

    protected function casts(): array
    {
        return [
            'registered' => 'bool',
        ];
    }
}
