<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Modules\Cdrs\Objects\SignedData;
use Ocpi\Modules\Cdrs\Objects\SignedValue;
use Ocpi\Modules\Cdrs\Objects\SignedValueCollection;

class SignedDataFactory
{
    public static function fromArray(?array $data = null): ?SignedData
    {
        if ($data === null) {
            return null;
        }
        return new SignedData(
            $data['encoding_method'],
            $data['encoding_method_version'] ?? null,
            $data['public_key'] ?? null,
            self::createSignedValueCollectionFromArray($data['signed_values']),
            $data['url'] ?? null,
        );
    }

    public static function createSignedValueCollectionFromArray(array $data): SignedValueCollection
    {
        $collection = new SignedValueCollection();
        foreach ($data as $value) {
            $collection->append(
                new SignedValue(
                    $value['nature'],
                    $value['plain_data'],
                    $value['signed_data'],
                )
            );
        }
        return $collection;
    }
}