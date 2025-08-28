<?php

namespace Ocpi\Modules\Locations\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\EnvironmentalImpactCategory;

class EnvironmentalImpact implements Arrayable
{
    public function __construct(
        private readonly EnvironmentalImpactCategory $category,
        private readonly float $amount,
    )
    {
    }

    /**
     * @return EnvironmentalImpactCategory
     */
    public function getCategory(): EnvironmentalImpactCategory
    {
        return $this->category;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'category' => $this->getCategory()->value,
            'amount' => $this->getAmount(),
        ];
    }
}