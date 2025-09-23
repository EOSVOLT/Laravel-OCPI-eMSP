<?php

namespace Ocpi\Modules\Locations\Factories;

use Ocpi\Modules\Locations\Objects\RegularHours;
use Ocpi\Modules\Locations\Objects\RegularHoursCollection;

class RegularHourFactory
{
    public static function fromArray(array $hour): RegularHours
    {
        return (new RegularHours(
            $hour['weekday'],
            $hour['period_begin'],
            $hour['period_end'],
        ));
    }

    public static function fromModelArray(array $hours): RegularHoursCollection
    {
        $collection = new RegularHoursCollection();
        foreach ($hours as $hour) {
            $collection->add(self::fromArray($hour));
        }
        return $collection;
    }
}