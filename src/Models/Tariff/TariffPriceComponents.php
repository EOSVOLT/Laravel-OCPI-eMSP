<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

/**
 * @property TariffDimensionType $type
 * @property float $price
 * @property ?float $vat
 * @property int $step_size
 */
class TariffPriceComponents extends Model
{
    protected $table = 'tariff_price_components';

    protected function casts(): array
    {
        return [
            'type' => TariffDimensionType::class,
        ];
    }
}
