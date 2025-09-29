<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Ocpi\Models\Tariff\TariffElement;
use Ocpi\Modules\Tariffs\Objects\TariffElement;
use Ocpi\Modules\Tariffs\Objects\TariffElementCollection;

class TariffElementFactory
{
    public static function fromModel(TariffElement $model): TariffElement
    {
        return new TariffElement(
            TariffPriceComponentFactory::fromCollection($model->priceComponents),
        )->setRestrictions(TariffRestrictionFactory::fromModel($model->restriction));
    }

    public static function fromCollection(TariffElement $collection): TariffElementCollection
    {
        $elements = new TariffElementCollection();
        foreach ($collection as $element) {
            $elements->add(self::fromModel($element));
        }
        return $elements;
    }
}