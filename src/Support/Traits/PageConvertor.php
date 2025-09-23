<?php

namespace Ocpi\Support\Traits;

trait PageConvertor
{
    public static function fromOffset(int $offset, int $limit): int
    {
        return $offset > 0 ? (int)ceil($offset / $limit) + 1 : 1;
    }
}