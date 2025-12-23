<?php

namespace Ocpi\Modules\Locations\Enums;

use Ocpi\Support\Traits\Enums\EnumArrayable;

enum EvseStatus: string
{
    use EnumArrayable;
    case AVAILABLE = 'AVAILABLE';
    case BLOCKED = 'BLOCKED';
    case CHARGING = 'CHARGING';
    case INOPERATIVE = 'INOPERATIVE';
    case OUTOFORDER = 'OUTOFORDER';
    case PLANNED = 'PLANNED';
    case REMOVED = 'REMOVED';
    case RESERVED = 'RESERVED';
    case UNKNOWN = 'UNKNOWN';
}
