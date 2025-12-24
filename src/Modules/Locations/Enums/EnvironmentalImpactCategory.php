<?php

namespace Ocpi\Modules\Locations\Enums;

use Ocpi\Support\Traits\Enums\EnumArrayable;

enum EnvironmentalImpactCategory: string
{
    use EnumArrayable;
    case NUCLEAR_WASTE = 'NUCLEAR_WASTE';
    case CARBON_DIOXIDE = 'CARBON_DIOXIDE';
}
