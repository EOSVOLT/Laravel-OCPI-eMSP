<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;


class TariffElements extends Model
{
    /**
     * @var string
     */
    protected $table = 'tariff_elements';

    /**
     * @return BelongsTo
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function restriction(): HasOne
    {
        return $this->hasOne(TariffRestrictions::class, 'tariff_restriction_id', 'id');
    }

    /**
     * @return HasManyThrough
     */
    public function priceComponents(): HasManyThrough
    {
        return $this->hasManyThrough(
            TariffPriceComponents::class,
            TariffElementPriceComponents::class,
            'tariff_element_id',
            'tariff_price_component_id',
            'id',
            'id'
        );
    }
}
