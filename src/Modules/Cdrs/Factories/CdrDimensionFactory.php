<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Modules\Cdrs\Objects\CdrDimension;
use Ocpi\Modules\Cdrs\Objects\CdrDimensionCollection;
use Ocpi\Support\Enums\CdrDimensionType;

class CdrDimensionFactory
{
    /**
     * @param array $data
     * @return CdrDimensionCollection
     */
    public static function collectionFromArray(array $data): CdrDimensionCollection
    {
        $collection = new CdrDimensionCollection();
        foreach ($data as $datum) {
            $collection->append(self::fromArray($datum));
        }
        return $collection;
    }

    /**
     * @param array $data
     * @return CdrDimension
     */
    public static function fromArray(array $data): CdrDimension
    {
        return new CdrDimension(
            CdrDimensionType::tryFrom($data['type']),
            $data['volume']
        );
    }
}