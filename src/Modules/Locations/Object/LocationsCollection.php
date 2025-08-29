<?php

namespace Ocpi\Modules\Locations\Object;

use Ocpi\Helpers\TypeCollection;
use Ocpi\Models\Locations\Location;

class LocationsCollection extends TypeCollection
{
    protected string $type = Location::class;
}