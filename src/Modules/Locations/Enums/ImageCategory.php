<?php

namespace Ocpi\Modules\Locations\Enums;

use Eosvolt\Foundation\EnumArrayable;

enum ImageCategory: string
{
    use EnumArrayable;
    case CHARGER = 'CHARGER';
    case ENTRANCE = 'ENTRANCE';
    case LOCATION = 'LOCATION';
    case NETWORK = 'NETWORK';
    case OPERATOR = 'OPERATOR';
    case OTHER = 'OTHER';
    case OWNER = 'OWNER';
}
