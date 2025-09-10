<?php

namespace Ocpi\Modules\Locations\Objects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\ConnectorFormat;
use Ocpi\Modules\Locations\Enums\ConnectorType;
use Ocpi\Modules\Locations\Enums\PowerType;

class Connector implements Arrayable
{
    /**
     * @var int|null
     */
    protected ?int $maxElectricPower = null;
    /**
     * @var array
     */
    protected array $tariffIds = [];
    /**
     * @var string|null
     */
    protected ?string $termsAndConditions = null;

    /**
     * @param string $connector_id
     * @param ConnectorType $standard
     * @param ConnectorFormat $format
     * @param PowerType $powerType
     * @param int $maxVoltage
     * @param int $maxAmperage
     * @param Carbon $lastUpdated
     * @param string|null $id
     */
    public function __construct(
        private readonly int $evseId,
        private readonly string $connector_id,
        private readonly ConnectorType $standard,
        private readonly ConnectorFormat $format,
        private readonly PowerType $powerType,
        private readonly int $maxVoltage,
        private readonly int $maxAmperage,
        private readonly Carbon $lastUpdated,
        private readonly ?string $id = null,
    ) {
    }

    /**
     * @return int|null
     */
    public function getMaxElectricPower(): ?int
    {
        return $this->maxElectricPower;
    }

    /**
     * @param int|null $maxElectricPower
     *
     * @return $this
     */
    public function setMaxElectricPower(?int $maxElectricPower): self
    {
        $this->maxElectricPower = $maxElectricPower;
        return $this;
    }

    /**
     * @return array
     */
    public function getTariffIds(): array
    {
        return $this->tariffIds;
    }

    /**
     * @param array $tariffIds
     *
     * @return $this
     */
    public function setTariffIds(array $tariffIds): self
    {
        $this->tariffIds = $tariffIds;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTermsAndConditions(): ?string
    {
        return $this->termsAndConditions;
    }

    /**
     * @param string|null $termsAndConditions
     *
     * @return $this
     */
    public function setTermsAndConditions(?string $termsAndConditions): self
    {
        $this->termsAndConditions = $termsAndConditions;
        return $this;
    }

    /**
     * @return string
     */
    public function getConnectorId(): string
    {
        return $this->connector_id;
    }

    /**
     * @return ConnectorType
     */
    public function getStandard(): ConnectorType
    {
        return $this->standard;
    }

    /**
     * @return ConnectorFormat
     */
    public function getFormat(): ConnectorFormat
    {
        return $this->format;
    }

    /**
     * @return PowerType
     */
    public function getPowerType(): PowerType
    {
        return $this->powerType;
    }

    /**
     * @return int
     */
    public function getMaxVoltage(): int
    {
        return $this->maxVoltage;
    }

    /**
     * @return int
     */
    public function getMaxAmperage(): int
    {
        return $this->maxAmperage;
    }

    /**
     * @return Carbon
     */
    public function getLastUpdated(): Carbon
    {
        return $this->lastUpdated;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEvseId(): int
    {
        return $this->evseId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'evse_id' => $this->getEvseId(),
            'connector_id' => $this->getConnectorId(),
            'standard' => $this->getStandard()->name,
            'format' => $this->getFormat()->name,
            'power_type' => $this->getPowerType()->name,
            'max_voltage' => $this->getMaxVoltage(),
            'max_amperage' => $this->getMaxAmperage(),
            'max_electric_power' => $this->getMaxElectricPower(),
            'tariff_ids' => $this->getTariffIds(),
            'terms_and_conditions' => $this->getTermsAndConditions(),
            'last_updated' => $this->getLastUpdated()->toISOString(),
        ];
    }
}