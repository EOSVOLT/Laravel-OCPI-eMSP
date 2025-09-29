<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

/**
 * @property int $id
 * @property TariffDimensionType $dimension_type
 * @property float $price
 * @property ?float $vat
 * @property int $step_size
 */
class TariffPriceComponents extends Model
{
    protected $fillable = [
        'vat',
        'step_size',
        'price',
        'dimension_type',
    ];
    protected $table = 'tariff_price_components';

    protected function casts(): array
    {
        return [
            'dimension_type' => TariffDimensionType::class,
        ];
    }
}
