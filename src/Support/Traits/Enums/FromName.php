<?php

namespace Ocpi\Support\Traits\Enums;

trait FromName
{
    public static function fromName($name): ?self
    {
        return defined('self::'.$name) ? constant('self::'.$name) : null;
    }
}
