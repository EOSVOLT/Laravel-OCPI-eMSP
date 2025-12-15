<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Ocpi\Helpers\PaginatedCollection;
class CdrCollection extends PaginatedCollection
{
    protected string $type = Cdr::class;
}