<?php

namespace Ocpi\Modules\Tokens\Objects;

use Ocpi\Helpers\PaginatedCollection;

class CommandTokenCollection extends PaginatedCollection
{
    protected string $type = CommandToken::class;
}