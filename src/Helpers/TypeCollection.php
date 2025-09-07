<?php

namespace Ocpi\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class TypeCollection extends \ArrayIterator implements Arrayable
{
    protected string $type;

    public function add(mixed $item): void
    {
        if (!$item instanceof $this->type) {
            throw new InvalidArgumentException(sprintf('Collection type for %s is not specified!', get_class($this)));
        }

        parent::append($item);
    }

    public function pluck($value, $key = null): array
    {
        $return = [];
        $camelValue = $this->snakeToCamel($value);
        foreach ($this->getArrayCopy() as $item) {
            if (is_null($key)) {
                $return[] = $item->{'get' . ucfirst($camelValue)}();
            } else {
                $return[$key] = $item->{'get' . ucfirst($camelValue)}();
            }
        }
        return $return;
    }

    private function snakeToCamel(string $value): string
    {
        return str_replace('_', '', ucwords($value, '_'));
    }

    public function merge(Arrayable $collection): static
    {
        if (get_class($this) !== get_class($collection)) {
            throw new InvalidArgumentException('Collections must be of the same type to merge.');
        }

        $merged = array_merge($this->getArrayCopy(), $collection->getArrayCopy());
        return new static($merged);
    }
}