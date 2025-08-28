<?php

namespace Ocpi\Modules\Locations\Enums;

enum ImageCategory: string
{
    case CHARGER = 'CHARGER';
    case ENTRANCE = 'ENTRANCE';
    case LOCATION = 'LOCATION';
    case NETWORK = 'NETWORK';
    case OPERATOR = 'OPERATOR';
    case OTHER = 'OTHER';
    case OWNER = 'OWNER';
}
