<?php

namespace Ocpi\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

abstract class TypeCollection extends \ArrayIterator implements Arrayable
{
    protected string $type;
    protected const array SCALAR_TYPES = [
        'integer',
        'double',
        'string',
        'array',
    ];

    public function __construct(array $array = [])
    {
        if ('' === $this->type) {
            throw new InvalidArgumentException(sprintf('Collection type for %s is not specified!', get_class($this)));
        }

        $isScalar = in_array($this->type, self::SCALAR_TYPES, true);

        foreach ($array as $item) {
            if (
                (!$isScalar && !$item instanceof $this->type)
                || ($isScalar && gettype($item) !== $this->type)
            ) {
                throw new InvalidArgumentException(sprintf('All elements of array should be type of %s!', $this->type));
            }
        }

        parent::__construct($array);
    }

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

    public function toArray(): array
    {
        if (in_array($this->type, self::SCALAR_TYPES, true)) {
            return $this->getArrayCopy();
        }

        $return = [];
        foreach ($this->getArrayCopy() as $item) {
            $return[] = $item->toArray();
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function first(): mixed
    {
        $this->rewind();
        return $this->current();
    }

    /**
     * @return mixed
     */
    public function last(): mixed
    {
        $array = $this->getArrayCopy();
        return end($array) ?: null;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function map(callable $callback): static
    {
        $mappedItems = [];

        foreach ($this->getArrayCopy() as $item) {
            $result = $callback($item);
            if (null === $result) {
                continue;
            }
            if (get_class($result) === $this->type) {
                $mappedItems[] = $result;
                continue;
            }
            throw new InvalidArgumentException(sprintf('All elements should be type of %s!', $this->type));
        }

        return new static($mappedItems);
    }

    public function sum(string $field): float|int
    {
        $sum = 0;
        foreach ($this->getArrayCopy() as $item) {
            $sum += $item->{'get' . ucfirst($field)}();
        }
        return $sum;
    }

    public function min(string $field): float|int
    {
        $values = [];
        foreach ($this->getArrayCopy() as $item) {
            $values[] = $item->{'get' . ucfirst($field)}();
        }

        if (empty(array_filter($values))) {
            throw new InvalidArgumentException('Cannot find minimum of empty collection');
        }

        return min($values);
    }

    public function max(string $field): float|int
    {
        $values = [];
        foreach ($this->getArrayCopy() as $item) {
            $values[] = $item->{'get' . ucfirst($field)}();
        }

        if (empty(array_filter($values))) {
            throw new InvalidArgumentException('Cannot find minimum of empty collection');
        }

        return max($values);
    }
}