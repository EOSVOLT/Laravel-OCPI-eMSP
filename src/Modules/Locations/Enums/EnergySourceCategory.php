<?php

namespace Ocpi\Modules\Locations\Enums;

/**
 * Categories of energy sources.
 */
enum EnergySourceCategory: string
{
    case NUCLEAR = 'NUCLEAR';
    case GENERAL_FOSSIL = 'GENERAL_FOSSIL';
    case COAL = 'COAL';
    case GAS = 'GAS';
    case GENERAL_GREEN = 'GENERAL_GREEN';
    case SOLAR = 'SOLAR';
    case WIND = 'WIND';
    case WATER = 'WATER';
}
