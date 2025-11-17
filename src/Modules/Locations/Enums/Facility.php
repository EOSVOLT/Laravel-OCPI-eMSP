<?php

namespace Ocpi\Modules\Locations\Enums;

use Eosvolt\Foundation\EnumArrayable;

enum Facility: string
{
    use EnumArrayable;
    case AIRPORT = 'AIRPORT';
    case BIKE_SHARING = 'BIKE_SHARING';
    case BUS_STOP = 'BUS_STOP';
    case CAFE = 'CAFE';
    case CARPOOL_PARKING = 'CARPOOL_PARKING';
    case FUEL_STATION = 'FUEL_STATION';
    case HOTEL = 'HOTEL';
    case MALL = 'MALL';
    case METRO_STATION = 'METRO_STATION';
    case MUSEUM = 'MUSEUM';
    case NATURE = 'NATURE';
    case PARKING_LOT = 'PARKING_LOT';
    case RECREATION_AREA = 'RECREATION_AREA';
    case RESTAURANT = 'RESTAURANT';
    case SPORT = 'SPORT';
    case SUPERMARKET = 'SUPERMARKET';
    case TAXI_STAND = 'TAXI_STAND';
    case TRAIN_STATION = 'TRAIN_STATION';
    case TRAM_STOP = 'TRAM_STOP';
    case WIFI = 'WIFI';
}
