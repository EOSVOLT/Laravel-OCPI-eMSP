<?php

namespace Ocpi\Modules\Locations\Enums;

enum ParkingRestriction: string
{
    case EV_ONLY = 'EV_ONLY';
    case PLUGGED = 'PLUGGED';
    case DISABLED = 'DISABLED';
    case CUSTOMERS = 'CUSTOMERS';
    case MOTORCYCLES = 'MOTORCYCLES';
}
