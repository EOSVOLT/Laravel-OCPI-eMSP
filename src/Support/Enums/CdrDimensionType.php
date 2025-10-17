<?php

namespace Ocpi\Support\Enums;

enum CdrDimensionType: string
{
    case CURRENT = 'CURRENT';
    case ENERGY = 'ENERGY';
    case ENERGY_EXPORT = 'ENERGY_EXPORT';
    case ENERGY_IMPORT = 'ENERGY_IMPORT';
    case MAX_CURRENT = 'MAX_CURRENT';
    case MIN_CURRENT = 'MIN_CURRENT';
    case MAX_POWER = 'MAX_POWER';
    case MIN_POWER = 'MIN_POWER';
    case PARKING_TIME = 'PARKING_TIME';
    case POWER = 'POWER';
    case RESERVATION_TIME = 'RESERVATION_TIME';
    case STATE_OF_CHARGE = 'STATE_OF_CHARGE';
    case TIME = 'TIME';
}
