<?php

namespace Ocpi\Modules\Locations\Enums;

use Eosvolt\Foundation\EnumArrayable;

enum TokenType: string
{
    use EnumArrayable;
    case AD_HOC_USER = 'AD_HOC_USER';
    case APP_USER = 'APP_USER';
    case OTHER = 'OTHER';
    case RFID = 'RFID';
}