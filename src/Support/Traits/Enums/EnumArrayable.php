<?php

namespace Ocpi\Support\Traits\Enums;

trait EnumArrayable
{
    public static function stringCases(): array
    {
        $return = [];
        foreach (self::cases() as $case) {
            $return[] = $case->value;
        }
        return $return;
    }
}