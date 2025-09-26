<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Ocpi\Models\Tariff\Tariff;
use Ocpi\Support\Enums\Role;

class TariffFactory
{
    public static function fromModel(Tariff $model): \Ocpi\Modules\Tariffs\Objects\Tariff
    {
        $party = $model->parties->first();
        $role = $party->roles->where('role', Role::CPO)->first();
        return new \Ocpi\Modules\Tariffs\Objects\Tariff(
            $role->country_code,
            $role->code,
            $model->external_id,
            $model->currency,
            TariffElementFactory::fromCollection($model->elements),
            $model->updated_at
        )
            ->setMinPrice(PriceFactory::fromData($model->min_price_excl_vat, $model->min_price_incl_vat))
            ->setMaxPrice(PriceFactory::fromData($model->max_price_excl_vat, $model->max_price_incl_vat));
    }
}