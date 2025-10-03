<?php

namespace Ocpi\Modules\Tariffs\Enums;

enum TariffDimensionType: string
{
    case ENERGY = 'ENERGY';
    case FLAT = 'FLAT';
    case PARKING_TIME = 'PARKING_TIME';
    case TIME = 'TIME';
}

