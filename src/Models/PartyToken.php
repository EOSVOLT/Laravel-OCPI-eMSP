<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property Party $party
 * @property int $party_id
 * @property string $token
 * @property bool $registered
 */
class PartyToken extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'party_id',
        'token_a',
        'token_b',
        'token_c',
        'registered',
    ];

    protected $attributes = [
        'registered' => false,
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    protected function casts(): array
    {
        return [
            'registered' => 'bool',
        ];
    }

}