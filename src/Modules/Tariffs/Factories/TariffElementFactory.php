<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\Tariff\TariffElement as TariffElementModel;
use Ocpi\Modules\Tariffs\Objects\TariffElement;
use Ocpi\Modules\Tariffs\Objects\TariffElementCollection;

class TariffElementFactory
{
    public static function fromModel(TariffElementModel $model): TariffElement
    {
        $element = new TariffElement(TariffPriceComponentFactory::fromCollection($model->priceComponents));
        if (null !== $model->restriction) {
            $element->setRestrictions(TariffRestrictionFactory::fromModel($model->restriction));
        }
        return $element;
    }

    public static function fromCollection(Collection $collection): TariffElementCollection
    {
        $elements = new TariffElementCollection();
        foreach ($collection as $element) {
            $elements->add(self::fromModel($element));
        }
        return $elements;
    }
}