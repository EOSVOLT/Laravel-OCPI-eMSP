<?php

namespace Ocpi\Modules\Locations\Factories;

use Ocpi\Modules\Locations\Objects\Hours;

class HourFactory
{
    public static function fromArray(array $hour): Hours
    {
        $hoursObj = new Hours($hour['twentyfourseven'] ?? false);
        if (true === $hoursObj->isTwentyfourseven()) {
            return $hoursObj;
        }
        $regularHours = RegularHourFactory::fromModelArray($hour['regular_hours'] ?? []);

        return new Hours(false)->setRegularHours($regularHours);
    }

}