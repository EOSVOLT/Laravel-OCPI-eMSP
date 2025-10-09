<?php

namespace Ocpi\Modules\Locations\Factories;

use Ocpi\Modules\Locations\Objects\BusinessDetails;

class BusinessModelFactory
{
    public static function fromArray(array $data): BusinessDetails
    {
        $businessDetail = new BusinessDetails($data['name'] ?? "")
            ->setWebsite($data['website'] ?? null);
        if (isset($data['logo'])) {
            $businessDetail->setLogo(ImageFactory::fromArray($data['logo']));
        }
        return $businessDetail;
    }
}