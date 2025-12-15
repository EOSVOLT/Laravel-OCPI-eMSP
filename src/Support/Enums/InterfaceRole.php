<?php

namespace Ocpi\Support\Enums;

use Eosvolt\Foundation\EnumArrayable;

enum InterfaceRole: string
{
    use EnumArrayable;
    case SENDER = 'SENDER';
    case RECEIVER = 'RECEIVER';
}
