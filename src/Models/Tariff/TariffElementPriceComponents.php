<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffElementPriceComponents extends Model
{
    protected $table = 'tariff_element_price_components';

    public function element(): BelongsTo
    {
        return $this->belongsTo(TariffElements::class, 'tariff_element_id', 'id');
    }

    public function priceComponent(): BelongsTo
    {
        return $this->belongsTo(TariffPriceComponents::class, 'tariff_price_component_id', 'id');
    }
}
