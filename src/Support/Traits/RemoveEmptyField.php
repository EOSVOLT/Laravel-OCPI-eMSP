<?php

namespace Ocpi\Support\Traits;

trait RemoveEmptyField
{
    public static function removeEmptyField(array $data): array
    {
        return array_filter($data, function ($value) {
            if (null === $value || $value === []) {
                return false;
            }
            return true;
        });
    }
}