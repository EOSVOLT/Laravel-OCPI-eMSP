<?php

namespace Ocpi\Modules\Locations\Enums;

use Ocpi\Support\Traits\Enums\EnumArrayable;

enum ParkingRestriction: string
{
    use EnumArrayable;
    case EV_ONLY = 'EV_ONLY';
    case PLUGGED = 'PLUGGED';
    case DISABLED = 'DISABLED';
    case CUSTOMERS = 'CUSTOMERS';
    case MOTORCYCLES = 'MOTORCYCLES';
}
