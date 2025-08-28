<?php

namespace Ocpi\Trait;

use Ocpi\Modules\Locations\Enums\ParkingRestriction;
use UnitEnum;

trait ValidateArrayEnum
{
    /**
     * @param array $array
     * @param array $enumCases
     *
     * @return void
     */
    public static function validateArrayEnum(array $array, array $enumCases): void
    {
        $validValues = array_values($enumCases);
        foreach ($array as $value) {
            if (!in_array($value, $validValues)) {
                throw new \InvalidArgumentException('Value must be one of ' . implode(',', $validValues));
            }
        }
    }
}
