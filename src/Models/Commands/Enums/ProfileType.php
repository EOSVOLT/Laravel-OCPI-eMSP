<?php

namespace Ocpi\Models\Commands\Enums;

enum ProfileType: string
{
    case CHEAP = 'CHEAP';
    case FAST = 'FAST';
    case GREEN = 'GREEN';
    case REGULAR = 'REGULAR';
}
