<?php

namespace Ocpi\Modules\Credentials\Object;

use Ocpi\Helpers\PaginatedCollection;

class PartyCollection extends PaginatedCollection
{
    protected string $type = Party::class;
}
