<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Illuminate\Support\Carbon;
use Ocpi\Modules\Cdrs\Objects\CdrDimensionCollection;
use Ocpi\Modules\Cdrs\Objects\ChargingPeriod;
use Ocpi\Modules\Cdrs\Objects\ChargingPeriodCollection;

class ChargingPeriodFactory
{
    /**
     * @param array $data
     * @return ChargingPeriodCollection
     */
    public static function collectionFromArray(array $data): ChargingPeriodCollection
    {
        $collection = new ChargingPeriodCollection();
        foreach ($data as $datum) {
            $collection->append(self::fromArray($datum));
        }
        return $collection;
    }

    /**
     * @param array $data
     * @return ChargingPeriod
     */
    public static function fromArray(array $data): ChargingPeriod
    {
        return new ChargingPeriod(
            Carbon::createFromTimeString($data['start_date_time']),
            CdrDimensionFactory::collectionFromArray($data['dimensions']),
            $data['tariff_id']
        );
    }
}