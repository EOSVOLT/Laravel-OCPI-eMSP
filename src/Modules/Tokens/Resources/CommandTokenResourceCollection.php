<?php

namespace Ocpi\Modules\Tokens\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Ocpi\Modules\Tokens\Objects\CommandTokenCollection;

class CommandTokenResourceCollection extends ResourceCollection
{
    public function __construct(CommandTokenCollection $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @param Request|null $request
     *
     * @return array
     */
    public function toArray(?Request $request = null): array
    {
        $data = [];
        foreach ($this->resource as $commandToken) {
            $data[] = new CommandTokenResource($commandToken)->toArray();
        }
        return $data;
    }
}
