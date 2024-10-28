<?php

namespace Ocpi\Support\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('ocpi.database.connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('ocpi.database.table.prefix').parent::getTable());
        }

        parent::__construct($attributes);
    }
}
