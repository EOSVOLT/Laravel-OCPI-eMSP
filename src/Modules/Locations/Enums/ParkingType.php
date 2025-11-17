<?php

namespace Ocpi\Modules\Locations\Enums;

use Eosvolt\Foundation\EnumArrayable;

enum ParkingType:string
{
    use EnumArrayable;
    case ALONG_MOTORWAY = 'ALONG_MOTORWAY';
    case PARKING_GARAGE = 'PARKING_GARAGE';
    case PARKING_LOT = 'PARKING_LOT';
    case ON_DRIVEWAY = 'ON_DRIVEWAY';
    case ON_STREET = 'ON_STREET';
    case UNDERGROUND_GARAGE = 'UNDERGROUND_GARAGE';
}
