<?php

namespace Ocpi\Support\Factories;

use Ocpi\Support\Objects\Price;

class PriceFactory
{
    /**
     * @param array $data
     * @return Price
     */
    public static function fromArray(array $data): Price
    {
        return new Price(
            $data['excl_vat'],
            $data['incl_vat'],
        );
    }
}