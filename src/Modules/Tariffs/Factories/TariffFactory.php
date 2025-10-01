<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Modules\Tariffs\Objects\TariffCollection;

class TariffFactory
{
    public static function fromModel(Tariff $model): \Ocpi\Modules\Tariffs\Objects\Tariff
    {
        $role = $model->party->role_cpo;
        $tariff = new \Ocpi\Modules\Tariffs\Objects\Tariff(
            $role->country_code,
            $role->code,
            $model->external_id,
            $model->currency,
            TariffElementFactory::fromCollection($model->elements),
            $model->updated_at
        );
        if ($model->min_price_excl_vat) {
            $tariff->setMinPrice(PriceFactory::fromData($model->min_price_excl_vat, $model->min_price_incl_vat));
        }
        if ($model->max_price_excl_vat) {
            $tariff->setMaxPrice(PriceFactory::fromData($model->max_price_excl_vat, $model->max_price_incl_vat));
        }
        return $tariff;
    }

    /**
     * @param Collection $collection
     *
     * @return TariffCollection
     */
    public static function fromCollection(Collection $collection): TariffCollection
    {
        $tariffs = new TariffCollection();
        foreach ($collection as $tariff) {
            $tariffs->add(self::fromModel($tariff));
        }
        return $tariffs;
    }
}