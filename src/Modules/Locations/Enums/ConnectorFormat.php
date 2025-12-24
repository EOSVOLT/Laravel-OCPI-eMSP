<?php

namespace Ocpi\Modules\Locations\Enums;

use Ocpi\Support\Traits\Enums\EnumArrayable;

enum ConnectorFormat: string
{
    use EnumArrayable;
    case SOCKET = 'SOCKET';
    case CABLE = 'CABLE';
}
