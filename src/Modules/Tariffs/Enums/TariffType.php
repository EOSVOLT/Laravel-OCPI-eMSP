<?php

namespace Ocpi\Modules\Tariffs\Enums;

use Ocpi\Support\Traits\Enums\EnumArrayable;

enum TariffType: string
{
    use EnumArrayable;
    case AD_HOC_PAYMENT = 'AD_HOC_PAYMENT';
    case PROFILE_CHEAP = 'PROFILE_CHEAP';
    case PROFILE_FAST = 'PROFILE_FAST';
    case PROFILE_GREEN = 'PROFILE_GREEN';
    case REGULAR = 'REGULAR';
}
