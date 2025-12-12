<?php

namespace Ocpi\Modules\Tokens\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Ocpi\Modules\Tokens\Objects\CommandTokenCollection;

class CommandTokenResourceCollection extends ResourceCollection
{
    public function __construct(CommandTokenCollection $resource)
    {
        parent::__construct($resource);
    }
}
