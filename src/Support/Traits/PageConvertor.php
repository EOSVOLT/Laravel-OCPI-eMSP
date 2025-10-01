<?php

namespace Ocpi\Support\Traits;

trait PageConvertor
{
    public static function fromOffset(int $offset, int $limit): int
    {
        return (int)floor($offset / $limit) + 1;
    }
}