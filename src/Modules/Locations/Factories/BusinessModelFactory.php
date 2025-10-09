<?php

namespace Ocpi\Modules\Locations\Factories;

use Ocpi\Modules\Locations\Objects\BusinessDetails;

class BusinessModelFactory
{
    public static function fromArray(array $data): BusinessDetails
    {
        return new BusinessDetails($data['name'])
            ->setWebsite($data['website'] ?? null)
            ->setLogo($data['logo'] ?? null);
    }
}