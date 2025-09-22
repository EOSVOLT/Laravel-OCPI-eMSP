<?php

namespace Ocpi\Modules\Locations\Client\V2_2_1;

use Ocpi\Modules\Locations\Client\V2_2_1\Resource as LocationsResource;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\SenderClient;

class CPOClient extends SenderClient
{
    use handlesLocation;
}