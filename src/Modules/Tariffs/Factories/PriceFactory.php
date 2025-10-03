<?php

namespace Ocpi\Modules\Tariffs\Factories;

use Ocpi\Modules\Tariffs\Objects\Price;

class PriceFactory
{
    public static function fromData(float $priceExclVat, ?float $priceInclVat = null): Price
    {
        return new Price(
            $priceExclVat,
        )->setInclVat($priceInclVat);
    }
}