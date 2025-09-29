<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ocpi\Models\Party;

class TariffsParties extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('ocpi.database.table.prefix') . 'tariffs_parties';
    }

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
