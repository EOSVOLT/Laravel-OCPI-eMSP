<?php

namespace Ocpi\Modules\Locations\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\EnergySourceCategory;

class EnergySource implements Arrayable
{
    public function __construct(
        private readonly EnergySourceCategory $source,
        private readonly float $percentage,
    )
    {
    }

    /**
     * @return EnergySourceCategory
     */
    public function getSource(): EnergySourceCategory
    {
        return $this->source;
    }

    /**
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->percentage;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'source' => $this->getSource()->value,
            'percentage' => $this->getPercentage(),
        ];
    }
}