<?php

namespace Ocpi\Models\Tariffs;

use Illuminate\Database\Eloquent\Model;
use Ocpi\Modules\Tariffs\Enums\ReservationRestrictionType;

/**
 * @property int $id
 * @property ?string $start_time
 * @property ?string $end_time
 * @property ?string $start_date
 * @property ?string $end_date
 * @property ?float $min_kwh
 * @property ?float $max_kwh
 * @property ?float $min_current
 * @property ?float $max_current
 * @property ?float $min_power
 * @property ?float $max_power
 * @property ?int $min_duration
 * @property ?int $max_duration
 * @property ?array $day_of_week
 * @property ?ReservationRestrictionType $reservation
 */
class TariffRestriction extends Model
{
    protected $guarded = [];

    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('ocpi.database.table.prefix') . 'tariff_restrictions';
    }

    protected function casts(): array
    {
        return [
            'reservation' => ReservationRestrictionType::class,
        ];
    }


}
