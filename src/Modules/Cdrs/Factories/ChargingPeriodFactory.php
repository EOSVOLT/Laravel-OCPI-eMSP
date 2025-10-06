<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Illuminate\Support\Carbon;
use Ocpi\Modules\Cdrs\DTO\ChargingPeriodDTO;
use Ocpi\Modules\Cdrs\DTO\ChargingPeriodDTOCollection;

class ChargingPeriodFactory
{
    /**
     * @param array|null $data
     * @return ChargingPeriodDTOCollection
     */
    public static function collectionFromArray(?array $data): ChargingPeriodDTOCollection
    {
        $collection = new ChargingPeriodDTOCollection();
        foreach ($data ?? [] as $datum) {
            $collection->append(self::fromArray($datum));
        }
        return $collection;
    }

    /**
     * @param array $data
     * @return ChargingPeriodDTO
     */
    public static function fromArray(array $data): ChargingPeriodDTO
    {
        return new ChargingPeriodDTO(
            Carbon::createFromTimeString($data['start_date_time']),
            CdrDimensionFactory::collectionFromArray($data['dimensions']),
            $data['tariff_id']
        );
    }
}