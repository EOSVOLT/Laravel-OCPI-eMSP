<?php

namespace Ocpi\Modules\Cdrs\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Support\Enums\CdrDimensionType;

readonly class CdrDimensionDTO implements Arrayable
{

    /**
     * @param CdrDimensionType $type
     * @param float $volume
     */
    public function __construct(private CdrDimensionType $type, private float $volume)
    {
    }

    /**
     * @return CdrDimensionType
     */
    public function getType(): CdrDimensionType
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType()->value,
            'volume' => $this->getVolume(),
        ];
    }
}