<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_role_id',
        'join_party_role_id',
    ];

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class);
    }

    public function join_party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class, 'join_party_role_id');
    }
}
