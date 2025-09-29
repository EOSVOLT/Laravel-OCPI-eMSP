<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ocpi\Models\Party;

class TariffsParty extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function party(): HasMany
    {
        return $this->hasMany(Party::class, 'id', 'party_id');
    }

    /**
     * @return HasMany
     */
    public function tariff(): HasMany
    {
        return $this->hasMany(Tariff::class, 'id', 'tariff_id');
    }
}
