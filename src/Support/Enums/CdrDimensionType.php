<?php

namespace Ocpi\Support\Enums;

enum CdrDimensionType: string
{
    case CURRENT = 'CURRENT';
    case ENERGY = 'ENERGY';
    case ENERGY_EXPORT = 'ENERGY_EXPORT';
    case ENERGY_IMPORT = 'ENERGY_IMPORT';
}
