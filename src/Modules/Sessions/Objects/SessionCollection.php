<?php

namespace Ocpi\Modules\Sessions\Objects;

use Ocpi\Helpers\PaginatedCollection;

class SessionCollection extends PaginatedCollection
{
    protected string $type = Session::class;
}