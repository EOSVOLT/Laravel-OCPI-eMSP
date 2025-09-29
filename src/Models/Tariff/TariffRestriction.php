<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;

/**
 * @property ?string $start_time
 * @property ?string $end_time
 * @property ?string $start_date
 * @property ?string $end_date
 * @property ?float $min_kwh
 * @property ?float $max_kwh
 * @property ?float min_current
 * @property ?float max_current
 * @property ?float min_power
 * @property ?float max_power
 * @property ?int min_duration
 * @property ?int max_duration
 * @property ?array day_of_week
 */
class TariffRestriction extends Model
{
    protected $guarded = [];


}
