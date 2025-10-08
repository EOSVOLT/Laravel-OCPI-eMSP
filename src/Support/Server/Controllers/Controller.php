<?php

namespace Ocpi\Support\Server\Controllers;

use Ocpi\Support\Traits\Server\InterfaceRoleTrait;
use Ocpi\Support\Traits\Server\Response as ServerResponse;

abstract class Controller
{
    use ServerResponse, InterfaceRoleTrait;
}
