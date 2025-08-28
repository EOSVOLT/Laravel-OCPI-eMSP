<?php

namespace Ocpi\Helpers;

use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class TypeCollection extends Collection
{
    protected string $type;

    public function add(mixed $item): void
    {
        if (!$item instanceof $this->type) {
            throw new InvalidArgumentException(sprintf('Collection type for %s is not specified!', get_class($this)));
        }

        parent::add($item);
    }
}