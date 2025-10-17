<?php

namespace Ocpi\Support\Factories;

use Ocpi\Support\Objects\Price;

class PriceFactory
{
    /**
     * @param array|null $data
     * @return Price|null
     */
    public static function fromArray(?array $data = null): ?Price
    {
        if (null === $data) {
            return null;
        }
        return new Price(
            $data['excl_vat'],
            $data['incl_vat'],
        );
    }
}