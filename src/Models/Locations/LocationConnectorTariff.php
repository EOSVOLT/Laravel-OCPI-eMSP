<?php

namespace Ocpi\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationConnectorTariff extends Model
{
    use HasFactory;
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
