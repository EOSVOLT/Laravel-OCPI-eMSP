<?php

namespace Ocpi\Modules\Locations\Objects;


use Ocpi\Helpers\PaginatedCollection;

class LocationsCollection extends PaginatedCollection
{
    protected string $type = Location::class;
}