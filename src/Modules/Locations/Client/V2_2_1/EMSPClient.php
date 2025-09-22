<?php

namespace Ocpi\Modules\Locations\Client\V2_2_1;

use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\ReceiverClient;

class EMSPClient extends ReceiverClient
{
    use handlesLocation;

    /**
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return parent::resolveBaseUrl(). self::getLocationPath('2.2.1');
    }
}