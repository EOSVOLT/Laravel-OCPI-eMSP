<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

/**
 * @property int $id
 * @property TariffDimensionType $dimension_type
 * @property float $price_excl_vat
 * @property float $price_incl_vat
 * @property ?float $vat
 * @property int $step_size
 */
class TariffPriceComponents extends Model
{
    protected $guarded = [];
    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('ocpi.database.table.prefix').'tariff_price_components';
    }

    protected function casts(): array
    {
        return [
            'dimension_type' => TariffDimensionType::class,
        ];
    }
}
