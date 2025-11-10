<?php

namespace Ocpi\Models\Tariffs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $tariff_id
 * @property int $tariff_restriction_id
 * @property null|TariffRestriction $restriction
 */
class TariffElement extends Model
{
    protected $guarded = [];

    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('ocpi.database.table.prefix').'tariff_elements';
    }

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
        return $this->hasOne(TariffRestriction::class, 'id', 'tariff_restriction_id');
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
            'id',
            'id',
            'tariff_price_component_id'
        );
    }
}
