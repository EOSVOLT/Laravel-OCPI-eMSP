<?php

namespace Ocpi\Modules\Locations\Enums;

enum PowerType: string
{
    case AC_1_PHASE = 'AC_1_PHASE';
    case AC_2_PHASE = 'AC_2_PHASE';
    case AC_2_PHASE_SPLIT = 'AC_2_PHASE_SPLIT';
    case AC_3_PHASE = 'AC_3_PHASE';
    case DC = 'DC';
}
