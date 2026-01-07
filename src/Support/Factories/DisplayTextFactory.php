<?php

namespace Ocpi\Support\Factories;

use Ocpi\Support\Objects\DisplayText;
use Ocpi\Support\Objects\DisplayTextCollection;

class DisplayTextFactory
{
    public static function fromArray(array $data): DisplayText
    {
        return new DisplayText($data['language'], $data['text']);
    }

    public static function fromArrayCollection(array $data): DisplayTextCollection
    {
        $collection = new DisplayTextCollection();
        foreach ($data as $datum) {
            $collection->append(self::fromArray($datum));
        }
        return $collection;
    }
}