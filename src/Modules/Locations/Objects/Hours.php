<?php

namespace Ocpi\Modules\Locations\Objects;

use Illuminate\Contracts\Support\Arrayable;

class Hours implements Arrayable
{
    /**
     * @var RegularHoursCollection|null
     */
    private ?RegularHoursCollection $regularHours = null;
    /**
     * @var ExceptionalPeriodCollection|null
     */
    private ?ExceptionalPeriodCollection $exceptionalOpenings = null;
    /**
     * @var ExceptionalPeriodCollection|null
     */
    private ?ExceptionalPeriodCollection $exceptionalClosings = null;

    /**
     * @param bool $twentyfourseven
     */
    public function __construct(
        private readonly bool $twentyfourseven,
    ) {
    }

    /**
     * @return bool
     */
    public function isTwentyfourseven(): bool
    {
        return $this->twentyfourseven;
    }

    /**
     * @return RegularHoursCollection|null
     */
    public function getRegularHours(): ?RegularHoursCollection
    {
        return $this->regularHours;
    }

    /**
     * @param RegularHoursCollection|null $regularHours
     *
     * @return $this
     */
    public function setRegularHours(?RegularHoursCollection $regularHours): self
    {
        $this->regularHours = $regularHours;
        return $this;
    }

    /**
     * @return ExceptionalPeriodCollection|null
     */
    public function getExceptionalOpenings(): ?ExceptionalPeriodCollection
    {
        return $this->exceptionalOpenings;
    }

    /**
     * @param ExceptionalPeriodCollection|null $exceptionalOpenings
     *
     * @return $this
     */
    public function setExceptionalOpenings(?ExceptionalPeriodCollection $exceptionalOpenings): self
    {
        $this->exceptionalOpenings = $exceptionalOpenings;
        return $this;
    }

    /**
     * @return ExceptionalPeriodCollection|null
     */
    public function getExceptionalClosings(): ?ExceptionalPeriodCollection
    {
        return $this->exceptionalClosings;
    }

    /**
     * @param ExceptionalPeriodCollection|null $exceptionalClosings
     *
     * @return $this
     */
    public function setExceptionalClosings(?ExceptionalPeriodCollection $exceptionalClosings): self
    {
        $this->exceptionalClosings = $exceptionalClosings;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'twentyfourseven' => $this->isTwentyfourseven(),
            'regular_hours' => $this->getRegularHours()?->toArray(),
            'exceptional_openings' => $this->getExceptionalOpenings()?->toArray(),
        ];
    }
}