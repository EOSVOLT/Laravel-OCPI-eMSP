<?php

namespace Ocpi\Modules\Tariffs\Traits;

trait HandlesTariff
{
    public function getTariffPath(string $version): string
    {
        return 'ocpi/cpo/' . $version . '/tariffs';
    }
}