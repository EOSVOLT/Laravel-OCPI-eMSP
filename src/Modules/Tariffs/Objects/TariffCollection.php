<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Ocpi\Helpers\PaginatedCollection;

class TariffCollection extends PaginatedCollection
{
    protected string $type = Tariff::class;
}