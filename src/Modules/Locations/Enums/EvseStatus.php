<?php

namespace Ocpi\Modules\Locations\Enums;

enum EvseStatus: string
{
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
