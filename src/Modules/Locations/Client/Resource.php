<?php

namespace Ocpi\Modules\Locations\Client;

use Ocpi\Support\Client\Resource as OcpiResource;

class Resource extends OcpiResource
{
    public function get(): ?array
    {
        return $this->requestGetSend();
    }
}
