<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;

class ConnectorTariff extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function getTable()
    {
        return config('ocpi.database.table.prefix') . 'connector_tariffs';
    }
}
