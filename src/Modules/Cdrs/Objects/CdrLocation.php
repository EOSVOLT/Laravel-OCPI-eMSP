<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\ConnectorFormat;
use Ocpi\Modules\Locations\Enums\ConnectorType;
use Ocpi\Modules\Locations\Enums\PowerType;
use Ocpi\Modules\Locations\Objects\GeoLocation;

readonly class CdrLocation implements Arrayable
{

    /**
     * @param string $locationId
     * @param string|null $name
     * @param string $address
     * @param string $city
     * @param string|null $postalCode
     * @param string|null $state
     * @param string $countryCode
     * @param GeoLocation $coordinates
     * @param string $evseUid
     * @param string $evseId
     * @param string $connectorId
     * @param ConnectorType $connectorType
     * @param ConnectorFormat $connectorFormat
     * @param PowerType $powerType
     */
    public function __construct(
        private string $locationId,
        private ?string $name = null,
        private string $address,
        private string $city,
        private ?string $postalCode = null,
        private ?string $state = null,
        private string $countryCode,
        private GeoLocation $coordinates,
        private string $evseUid,
        private string $evseId,
        private string $connectorId,
        private ConnectorType $connectorType,
        private ConnectorFormat $connectorFormat,
        private PowerType $powerType,
    ) {
    }

    /**
     * @return string
     */
    public function getLocationId(): string
    {
        return $this->locationId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return GeoLocation
     */
    public function getCoordinates(): GeoLocation
    {
        return $this->coordinates;
    }

    /**
     * @return string
     */
    public function getEvseUid(): string
    {
        return $this->evseUid;
    }

    /**
     * @return string
     */
    public function getEvseId(): string
    {
        return $this->evseId;
    }

    /**
     * @return string
     */
    public function getConnectorId(): string
    {
        return $this->connectorId;
    }

    /**
     * @return ConnectorType
     */
    public function getConnectorType(): ConnectorType
    {
        return $this->connectorType;
    }

    /**
     * @return ConnectorFormat
     */
    public function getConnectorFormat(): ConnectorFormat
    {
        return $this->connectorFormat;
    }

    /**
     * @return PowerType
     */
    public function getPowerType(): PowerType
    {
        return $this->powerType;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getLocationId(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'city' => $this->getCity(),
            'postal_code' => $this->getPostalCode(),
            'state' => $this->getState(),
            'country' => $this->getCountryCode(),
            'coordinates' => $this->getCoordinates()->toArray(),
            'evse_uid' => $this->getEvseUid(),
            'evse_id' => $this->getEvseId(),
            'connector_id' => $this->getConnectorId(),
            'connector_standard' => $this->getConnectorType()->value,
            'connector_format' => $this->getConnectorFormat()->value,
            'connector_power_type' => $this->getPowerType()->value,
        ];
    }
}