<?php

namespace Ocpi\Modules\Locations\Factories;

use Ocpi\Modules\Locations\Enums\ImageCategory;
use Ocpi\Modules\Locations\Objects\Image;
use Ocpi\Modules\Locations\Objects\ImageCollection;

class ImageFactory
{
    public static function fromArray(array $image): Image
    {
        return new Image(
            $image['url'],
            ImageCategory::tryFrom($image['type']),
            $image['category'],
        );
    }

    public static function fromModelArray(array $images): ImageCollection
    {
        $collection = new ImageCollection();
        foreach ($images as $image) {
            $collection->add(self::fromArray($image));
        }
        return $collection;
    }
}