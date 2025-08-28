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
            throw new InvalidArgumentException();
        }

        parent::add($item);
    }
}