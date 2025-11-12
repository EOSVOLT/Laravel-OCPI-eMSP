<?php

namespace Ocpi\Models;

use Database\Factories\PartyTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property Party $party
 * @property int $party_id
 * @property int $party_role_id
 * @property PartyRole $party_role
 * @property string $name
 * @property string $token
 * @property bool $registered
 */
class PartyToken extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'party_id',
        'party_role_id',
        'name',
        'token',
        'registered',
    ];

    protected $attributes = [
        'registered' => false,
    ];

    protected static function newFactory(): PartyTokenFactory
    {
        return PartyTokenFactory::new();
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }

    protected function casts(): array
    {
        return [
            'registered' => 'bool',
        ];
    }

}