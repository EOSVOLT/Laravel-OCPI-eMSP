<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Modules\Cdrs\DTO\CdrDimensionDTO;
use Ocpi\Modules\Cdrs\DTO\CdrDimensionDTOCollection;
use Ocpi\Support\Enums\CdrDimensionType;

class CdrDimensionFactory
{
    /**
     * @param array $data
     * @return CdrDimensionDTOCollection
     */
    public static function collectionFromArray(array $data): CdrDimensionDTOCollection
    {
        $collection = new CdrDimensionDTOCollection();
        foreach ($data as $datum) {
            $collection->append(self::fromArray($datum));
        }
        return $collection;
    }

    /**
     * @param array $data
     * @return CdrDimensionDTO
     */
    public static function fromArray(array $data): CdrDimensionDTO
    {
        return new CdrDimensionDTO(
            CdrDimensionType::tryFrom($data['type']),
            $data['volume']
        );
    }
}