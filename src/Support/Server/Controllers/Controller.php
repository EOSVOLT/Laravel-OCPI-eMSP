<?php

namespace Ocpi\Support\Server\Controllers;

use Ocpi\Support\Enums\InterfaceRole;
use Ocpi\Support\Traits\Server\Response as ServerResponse;

abstract class Controller
{
    use ServerResponse;

    protected function getInterfaceRoleByModule(string $module): InterfaceRole
    {
        return match ($module) {
            'credentials', 'locations', 'cdrs', 'sessions', 'tariffs' => InterfaceRole::SENDER,
            'commands' => InterfaceRole::RECEIVER,
        };
    }
}
