<?php

namespace Ocpi\Modules\CDRs\Client;

use Ocpi\Support\Client\Resource as OcpiResource;

class Resource extends OcpiResource
{
    public function get(?array $query = null): ?array
    {
        return $this->requestGetSend(query: $query);
    }
}
