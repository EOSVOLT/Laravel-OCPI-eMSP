<?php

namespace Ocpi\Modules\Commands\Enums;

enum WhitelistType: string
{
    case ALWAYS = 'ALWAYS';
    case ALLOWED = 'ALLOWED';
    case ALLOWED_OFFLINE = 'ALLOWED_OFFLINE';
    case NEVER = 'NEVER';
}
