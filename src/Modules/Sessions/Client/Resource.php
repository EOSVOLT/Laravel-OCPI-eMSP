<?php

namespace Ocpi\Modules\Sessions\Client;

use Ocpi\Support\Client\Resource as OcpiResource;

class Resource extends OcpiResource
{
    public function all(): ?array
    {
        return $this->requestGetSend();
    }
}
