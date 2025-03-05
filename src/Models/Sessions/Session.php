<?php

namespace Ocpi\Models\Sessions;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Models\Model;
use Ocpi\Support\Traits\Models\HasCompositeKey;

class Session extends Model
{
    use HasCompositeKey;

    protected $primaryKey = [
        'party_role_id',
        'id',
    ];

    protected $fillable = [
        'party_role_id',
        'id',
        'object',
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

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }
}
