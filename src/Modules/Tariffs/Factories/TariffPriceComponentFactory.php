<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\Tariff\TariffPriceComponents;
use Ocpi\Modules\Tariffs\Objects\TariffPriceComponent;
use Ocpi\Modules\Tariffs\Objects\TariffPriceComponentCollection;

class TariffPriceComponentFactory
{
    /**
     * @param TariffPriceComponents $model
     *
     * @return TariffPriceComponent
     */
    public static function fromModel(TariffPriceComponents $model): TariffPriceComponent
    {
        return new TariffPriceComponent(
            $model->id,
            $model->dimension_type,
            $model->price,
            $model->step_size
        )->setVat($model->vat);
    }

    /**
     * @param Collection $collection
     *
     * @return TariffPriceComponentCollection
     */
    public static function fromCollection(Collection $collection): TariffPriceComponentCollection
    {
        $priceComponents = new TariffPriceComponentCollection();
        foreach ($collection as $component) {
            $priceComponents->add(self::fromModel($component));
        }
        return $priceComponents;
    }
}