<?php

namespace Ocpi\Modules\Locations\Enums;

use Ocpi\Support\Traits\Enums\EnumArrayable;

enum PowerType: string
{
    use EnumArrayable;
    case AC_1_PHASE = 'AC_1_PHASE';
    case AC_2_PHASE = 'AC_2_PHASE';
    case AC_2_PHASE_SPLIT = 'AC_2_PHASE_SPLIT';
    case AC_3_PHASE = 'AC_3_PHASE';
    case DC = 'DC';
}
