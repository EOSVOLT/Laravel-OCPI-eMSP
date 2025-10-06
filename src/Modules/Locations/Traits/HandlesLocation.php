<?php

namespace Ocpi\Modules\Locations\Traits;

trait HandlesLocation
{
    public function getLocationPath(string $version): string
    {
        return 'ocpi/cpo/' . $version . '/locations';
    }
}
