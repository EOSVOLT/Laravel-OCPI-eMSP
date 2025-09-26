<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\Tariff\TariffPriceComponents;
use Ocpi\Modules\Tariffs\Objects\PriceComponent;
use Ocpi\Modules\Tariffs\Objects\PriceComponentCollection;

class TariffPriceComponentFactory
{
    /**
     * @param TariffPriceComponents $model
     *
     * @return PriceComponent
     */
    public static function fromModel(TariffPriceComponents $model): PriceComponent
    {
        return new PriceComponent(
            $model->type,
            $model->price,
            $model->step_size
        )->setVat($model->vat);
    }

    /**
     * @param Collection $collection
     *
     * @return PriceComponentCollection
     */
    public static function fromCollection(Collection $collection): PriceComponentCollection
    {
        $priceComponents = new PriceComponentCollection();
        foreach ($collection as $component) {
            $priceComponents->add(self::fromModel($component));
        }
        return $priceComponents;
    }
}