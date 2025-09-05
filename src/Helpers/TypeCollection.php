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

    public function pluck($value, $key = null): array
    {
        $return = [];
        $camelValue = $this->snakeToCamel($value);
        foreach ($this->items as $item) {
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
}