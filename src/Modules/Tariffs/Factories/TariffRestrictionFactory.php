<?php

namespace Ocpi\Modules\Tariffs\Factories;


use Ocpi\Models\Tariffs\TariffRestriction;

class TariffRestrictionFactory
{
    public static function fromModel(TariffRestriction $model): \Ocpi\Modules\Tariffs\Objects\TariffRestriction
    {
        return new \Ocpi\Modules\Tariffs\Objects\TariffRestriction($model->id)
            ->setStartTime($model->start_time)
            ->setEndTime($model->end_time)
            ->setStartDate($model->start_date)
            ->setEndDate($model->end_date)
            ->setMinKwh($model->min_kwh)
            ->setMaxKwh($model->max_kwh)
            ->setMinCurrent($model->min_current)
            ->setMaxCurrent($model->max_current)
            ->setMinPower($model->min_power)
            ->setMaxPower($model->max_power)
            ->setMinDuration($model->min_duration)
            ->setMaxDuration($model->max_duration)
            ->setDayOfWeek($model->day_of_week ?? []);
    }
}