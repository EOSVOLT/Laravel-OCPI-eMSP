<?php

namespace Ocpi\Modules\Locations\Enums;

use Eosvolt\Foundation\EnumArrayable;

enum ConnectorFormat: string
{
    use EnumArrayable;
    case SOCKET = 'SOCKET';
    case CABLE = 'CABLE';
}
