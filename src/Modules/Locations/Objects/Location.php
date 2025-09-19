<?php

namespace Ocpi\Modules\Locations\Objects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Credentials\Object\Party;
use Ocpi\Modules\Locations\Enums\Facility;
use Ocpi\Modules\Locations\Enums\ParkingType;
use Ocpi\Trait\ValidateArrayEnum;

class Location implements Arrayable
{
    use ValidateArrayEnum;

    /**
     * @var string|null
     */
    protected ?string $name = null;
    /**
     * @var string|null
     */
    protected ?string $postalCode = null;
    /**
     * @var string|null
     */
    protected ?string $state = null;
    /**
     * @var PublishTokenTypeCollection|null
     */
    protected ?PublishTokenTypeCollection $publishAllowedTo = null;
    /**
     * @var AdditionalGeoLocationCollection|null
     */
    protected ?AdditionalGeoLocationCollection $relatedLocations = null;
    /**
     * @var ParkingType|null
     */
    protected ?ParkingType $parkingType = null;
    /**
     * @var EvseCollection|null
     */
    protected ?EvseCollection $evses = null;
    /**
     * @var DisplayTextCollection|null
     */
    protected ?DisplayTextCollection $directions = null;
    /**
     * @var BusinessDetails|null
     */
    protected ?BusinessDetails $operator = null;
    /**
     * @var BusinessDetails|null
     */
    protected ?BusinessDetails $suboperator = null;
    /**
     * @var BusinessDetails|null
     */
    protected ?BusinessDetails $owner = null;
    /**
     * @var array
     */
    protected array $facilities = [];
    /**
     * @var Hours|null
     */
    protected ?Hours $openingTimes = null;
    /**
     * @var bool
     */
    protected bool $chargingWhenClosed = true;
    /**
     * @var ImageCollection|null
     */
    protected ?ImageCollection $images = null;
    /**
     * @var EnergyMix|null
     */
    protected ?EnergyMix $energyMix = null;

    /**
     * @var Party|null
     */
    private ?Party $party = null;

    /**
     * @param string $countryCode
     * @param int $partyId
     * @param string $externalId
     * @param bool $publish
     * @param string $address
     * @param string $city
     * @param string $country
     * @param GeoLocation $coordinates
     * @param string $timeZone
     * @param Carbon $lastUpdated
     * @param int|null $id
     */
    public function __construct(
        private readonly string $countryCode,
        private readonly int $partyId,
        private readonly string $externalId,
        private readonly bool $publish,
        private readonly string $address,
        private readonly string $city,
        private readonly string $country,
        private readonly GeoLocation $coordinates,
        private readonly string $timeZone,
        private readonly Carbon $lastUpdated,
        private readonly ?int $id = null,
    ) {
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return int
     */
    public function getPartyId(): int
    {
        return $this->partyId;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * @return bool
     */
    public function isPublish(): bool
    {
        return $this->publish;
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
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string|null $postalCode
     *
     * @return $this
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     *
     * @return $this
     */
    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return PublishTokenTypeCollection|null
     */
    public function getPublishAllowedTo(): ?PublishTokenTypeCollection
    {
        return $this->publishAllowedTo;
    }

    /**
     * @param PublishTokenTypeCollection|null $publishAllowedTo
     *
     * @return $this
     */
    public function setPublishAllowedTo(?PublishTokenTypeCollection $publishAllowedTo): self
    {
        $this->publishAllowedTo = $publishAllowedTo;
        return $this;
    }

    /**
     * @return AdditionalGeoLocationCollection|null
     */
    public function getRelatedLocations(): ?AdditionalGeoLocationCollection
    {
        return $this->relatedLocations;
    }

    /**
     * @param AdditionalGeoLocationCollection|null $relatedLocations
     *
     * @return $this
     */
    public function setRelatedLocations(?AdditionalGeoLocationCollection $relatedLocations): self
    {
        $this->relatedLocations = $relatedLocations;
        return $this;
    }

    /**
     * @return ParkingType|null
     */
    public function getParkingType(): ?ParkingType
    {
        return $this->parkingType;
    }

    /**
     * @param ParkingType|null $parkingType
     *
     * @return $this
     */
    public function setParkingType(?ParkingType $parkingType): self
    {
        $this->parkingType = $parkingType;
        return $this;
    }

    /**
     * @return EvseCollection|null
     */
    public function getEvses(): ?EvseCollection
    {
        return $this->evses;
    }

    /**
     * @param EvseCollection|null $evses
     *
     * @return $this
     */
    public function setEvses(?EvseCollection $evses): self
    {
        $this->evses = $evses;
        return $this;
    }

    /**
     * @return DisplayTextCollection|null
     */
    public function getDirections(): ?DisplayTextCollection
    {
        return $this->directions;
    }

    /**
     * @param DisplayTextCollection|null $directions
     *
     * @return $this
     */
    public function setDirections(?DisplayTextCollection $directions): self
    {
        $this->directions = $directions;
        return $this;
    }

    /**
     * @return BusinessDetails|null
     */
    public function getOperator(): ?BusinessDetails
    {
        return $this->operator;
    }

    /**
     * @param BusinessDetails|null $operator
     *
     * @return $this
     */
    public function setOperator(?BusinessDetails $operator): self
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return BusinessDetails|null
     */
    public function getSuboperator(): ?BusinessDetails
    {
        return $this->suboperator;
    }

    /**
     * @param BusinessDetails|null $suboperator
     *
     * @return $this
     */
    public function setSuboperator(?BusinessDetails $suboperator): self
    {
        $this->suboperator = $suboperator;
        return $this;
    }

    /**
     * @return BusinessDetails|null
     */
    public function getOwner(): ?BusinessDetails
    {
        return $this->owner;
    }

    /**
     * @param BusinessDetails|null $owner
     *
     * @return $this
     */
    public function setOwner(?BusinessDetails $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Hours|null
     */
    public function getOpeningTimes(): ?Hours
    {
        return $this->openingTimes;
    }

    /**
     * @param Hours|null $openingTimes
     *
     * @return $this
     */
    public function setOpeningTimes(?Hours $openingTimes): self
    {
        $this->openingTimes = $openingTimes;
        return $this;
    }

    /**
     * @return bool
     */
    public function isChargingWhenClosed(): bool
    {
        return $this->chargingWhenClosed;
    }

    /**
     * @param bool $chargingWhenClosed
     *
     * @return $this
     */
    public function setChargingWhenClosed(bool $chargingWhenClosed): self
    {
        $this->chargingWhenClosed = $chargingWhenClosed;
        return $this;
    }

    /**
     * @return ImageCollection|null
     */
    public function getImages(): ?ImageCollection
    {
        return $this->images;
    }

    /**
     * @param ImageCollection|null $images
     *
     * @return $this
     */
    public function setImages(?ImageCollection $images): self
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return EnergyMix|null
     */
    public function getEnergyMix(): ?EnergyMix
    {
        return $this->energyMix;
    }

    /**
     * @param EnergyMix|null $energyMix
     *
     * @return $this
     */
    public function setEnergyMix(?EnergyMix $energyMix): self
    {
        $this->energyMix = $energyMix;
        return $this;
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
    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    /**
     * @return Carbon
     */
    public function getLastUpdated(): Carbon
    {
        return $this->lastUpdated;
    }

    /**
     * @return array
     */
    public function getFacilities(): array
    {
        return $this->facilities;
    }

    /**
     * @param array $facilities
     *
     * @return $this
     */
    public function setFacilities(array $facilities): self
    {
        self::validateArrayEnum($facilities, Facility::cases());
        $this->facilities = $facilities;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Party|null
     */
    public function getParty(): ?Party
    {
        return $this->party;
    }


    /**
     * @param Party $party
     *
     * @return $this
     */
    public function setParty(Party $party): self
    {
        $this->party = $party;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'country_code' => $this->getCountryCode(),
            'party_id' => $this->getPartyId(),
            'external_id' => $this->getExternalId(),
            'publish' => $this->isPublish(),
            'publish_allowed_to' => $this->getPublishAllowedTo()?->toArray(),
            'name' => $this->getName(),
            'address' => $this->getAddress(),
            'city' => $this->getCity(),
            'postal_code' => $this->getPostalCode(),
            'state' => $this->getState(),
            'country' => $this->getCountry(),
            'coordinates' => $this->getCoordinates()->toArray(),
            'related_locations' => $this->getRelatedLocations()?->toArray(),
            'parking_type' => $this->getParkingType()?->value,
            'evses' => $this->getEvses()?->toArray(),
            'directions' => $this->getDirections()?->toArray(),
            'operator' => $this->getOperator()?->toArray(),
            'suboperator' => $this->getSuboperator()?->toArray(),
            'owner' => $this->getOwner()?->toArray(),
            'facilities' => $this->getFacilities(),
            'time_zone' => $this->getTimeZone(),
            'opening_times' => $this->getOpeningTimes()?->toArray(),
            'charging_when_closed' => $this->isChargingWhenClosed(),
            'images' => $this->getImages()?->toArray(),
            'energy_mix' => $this->getEnergyMix()?->toArray(),
            'last_updated' => $this->getLastUpdated()->toISOString(),
        ];
    }
}