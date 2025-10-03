<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Model;

class LocationConnectorTariff extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('ocpi.database.table.prefix').'location_connector_tariffs';
    }
}
