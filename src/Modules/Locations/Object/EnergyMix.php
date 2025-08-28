<?php

namespace Ocpi\Modules\Locations\Object;

use Illuminate\Contracts\Support\Arrayable;

class EnergyMix implements Arrayable
{
    /**
     * @var EnergySourceCollection|null
     */
    private ?EnergySourceCollection $energySources = null;
    /**
     * @var EnvironmentalImpactCollection|null
     */
    private ?EnvironmentalImpactCollection $environImpact = null;
    /**
     * @var string|null
     */
    private ?string $supplierName = null;
    /**
     * @var string|null
     */
    private ?string $energyProductName = null;

    /**
     * @param bool $isGreenEnergy
     */
    public function __construct(
        private readonly bool $isGreenEnergy,
    ) {
    }

    /**
     * @return EnergySourceCollection|null
     */
    public function getEnergySources(): ?EnergySourceCollection
    {
        return $this->energySources;
    }

    /**
     * @param EnergySourceCollection|null $energySources
     *
     * @return $this
     */
    public function setEnergySources(?EnergySourceCollection $energySources): self
    {
        $this->energySources = $energySources;
        return $this;
    }

    /**
     * @return EnvironmentalImpactCollection|null
     */
    public function getEnvironImpact(): ?EnvironmentalImpactCollection
    {
        return $this->environImpact;
    }

    /**
     * @param EnvironmentalImpactCollection|null $environImpact
     *
     * @return $this
     */
    public function setEnvironImpact(?EnvironmentalImpactCollection $environImpact): self
    {
        $this->environImpact = $environImpact;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSupplierName(): ?string
    {
        return $this->supplierName;
    }

    /**
     * @param string|null $supplierName
     *
     * @return $this
     */
    public function setSupplierName(?string $supplierName): self
    {
        $this->supplierName = $supplierName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEnergyProductName(): ?string
    {
        return $this->energyProductName;
    }

    /**
     * @param string|null $energyProductName
     *
     * @return $this
     */
    public function setEnergyProductName(?string $energyProductName): self
    {
        $this->energyProductName = $energyProductName;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGreenEnergy(): bool
    {
        return $this->isGreenEnergy;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'is_green_energy' => $this->isGreenEnergy(),
            'energy_sources' => $this->getEnergySources()?->toArray(),
            'environ_impact' => $this->getEnvironImpact()?->toArray(),
            'supplier_name' => $this->getSupplierName(),
            'energy_product_name' => $this->getEnergyProductName(),
        ];
    }
}