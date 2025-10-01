<?php

namespace Ocpi\Support\Traits;

trait RemoveEmptyField
{
    public static function removeEmptyField(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::removeEmptyField($value);
            }
        }

        // Then, filter out nulls and empty arrays
        return array_filter($data, static function ($value) {
            if ($value === null) {
                return false;
            }
            if (is_array($value)) {
                return $value !== []; // keep only non-empty arrays
            }
            return true; // keep scalars
        });
    }
}