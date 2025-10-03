<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;

class TariffRestriction implements Arrayable
{
    /**
     * @var string|null
     */
    private ?string $startTime = null; //([0-1][0-9]|2[0-3]):[0-5][0-9]
    /**
     * @var string|null
     */
    private ?string $endTime = null; //([0-1][0-9]|2[0-3]):[0-5][0-9]
    /**
     * @var string|null
     */
    private ?string $startDate = null; //([12][0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) 2015-12-24
    /**
     * @var string|null
     */
    private ?string $endDate = null; //([12][0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) 2015-12-24
    /**
     * @var float|null
     */
    private ?float $minKwh = null;
    /**
     * @var float|null
     */
    private ?float $maxKwh = null;
    /**
     * @var float|null
     */
    private ?float $minCurrent = null;
    /**
     * @var float|null
     */
    private ?float $maxCurrent = null;
    /**
     * @var float|null
     */
    private ?float $minPower = null;
    /**
     * @var float|null
     */
    private ?float $maxPower = null;
    /**
     * @var int|null
     */
    private ?int $minDuration = null;
    /**
     * @var int|null
     */
    private ?int $maxDuration = null;
    /**
     * @var array
     */
    private array $dayOfWeek = [];

    /**
     * @param int $id
     */
    public function __construct(
        private readonly int $id,
    ) {
    }

    /**
     * @return string|null
     */
    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    /**
     * @param string|null $startTime
     *
     * @return $this
     */
    public function setStartTime(?string $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    /**
     * @param string|null $endTime
     *
     * @return $this
     */
    public function setEndTime(?string $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    /**
     * @param string|null $startDate
     *
     * @return $this
     */
    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @param string|null $endDate
     *
     * @return $this
     */
    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinKwh(): ?float
    {
        return $this->minKwh;
    }

    /**
     * @param float|null $minKwh
     *
     * @return $this
     */
    public function setMinKwh(?float $minKwh): self
    {
        $this->minKwh = $minKwh;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxKwh(): ?float
    {
        return $this->maxKwh;
    }

    /**
     * @param float|null $maxKwh
     *
     * @return $this
     */
    public function setMaxKwh(?float $maxKwh): self
    {
        $this->maxKwh = $maxKwh;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinCurrent(): ?float
    {
        return $this->minCurrent;
    }

    /**
     * @param float|null $minCurrent
     *
     * @return $this
     */
    public function setMinCurrent(?float $minCurrent): self
    {
        $this->minCurrent = $minCurrent;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxCurrent(): ?float
    {
        return $this->maxCurrent;
    }

    /**
     * @param float|null $maxCurrent
     *
     * @return $this
     */
    public function setMaxCurrent(?float $maxCurrent): self
    {
        $this->maxCurrent = $maxCurrent;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinPower(): ?float
    {
        return $this->minPower;
    }

    /**
     * @param float|null $minPower
     *
     * @return $this
     */
    public function setMinPower(?float $minPower): self
    {
        $this->minPower = $minPower;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxPower(): ?float
    {
        return $this->maxPower;
    }

    /**
     * @param float|null $maxPower
     *
     * @return $this
     */
    public function setMaxPower(?float $maxPower): self
    {
        $this->maxPower = $maxPower;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinDuration(): ?int
    {
        return $this->minDuration;
    }

    /**
     * @param int|null $minDuration
     *
     * @return $this
     */
    public function setMinDuration(?int $minDuration): self
    {
        $this->minDuration = $minDuration;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxDuration(): ?int
    {
        return $this->maxDuration;
    }

    /**
     * @param int|null $maxDuration
     *
     * @return $this
     */
    public function setMaxDuration(?int $maxDuration): self
    {
        $this->maxDuration = $maxDuration;
        return $this;
    }

    /**
     * @return array
     */
    public function getDayOfWeek(): array
    {
        return $this->dayOfWeek;
    }

    /**
     * @param array $dayOfWeek
     *
     * @return $this
     */
    public function setDayOfWeek(array $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'start_time' => $this->getStartTime(),
            'end_time' => $this->getEndTime(),
            'start_date' => $this->getStartDate(),
            'end_date' => $this->getEndDate(),
            'min_kwh' => $this->getMinKwh(),
            'max_kwh' => $this->getMaxKwh(),
            'min_current' => $this->getMinCurrent(),
            'max_current' => $this->getMaxCurrent(),
            'min_power' => $this->getMinPower(),
            'max_power' => $this->getMaxPower(),
            'min_duration' => $this->getMinDuration(),
            'max_duration' => $this->getMaxDuration(),
            'day_of_week' => $this->getDayOfWeek(),
        ];
    }
}