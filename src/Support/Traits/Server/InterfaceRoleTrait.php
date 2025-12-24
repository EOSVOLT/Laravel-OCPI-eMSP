<?php

namespace Ocpi\Support\Traits\Server;

use Ocpi\Support\Enums\InterfaceRole;

trait InterfaceRoleTrait
{
    /**
     * @param string $module
     * @return InterfaceRole
     */
    public function getCPOInterfaceRoleByModule(string $module): InterfaceRole
    {
        return match ($module) {
            'credentials', 'locations', 'cdrs', 'sessions', 'tariffs' => InterfaceRole::SENDER,
            'commands' => InterfaceRole::RECEIVER,
        };
    }

    /**
     * @todo revisit when doing emsp.
     * @param string $module
     * @return InterfaceRole
     */
    public function getEMSPInterfaceRoleByModule(string $module): InterfaceRole
    {
        return match ($module) {
            'credentials', 'locations', 'cdrs', 'sessions', 'tariffs' => InterfaceRole::RECEIVER,
            'commands', 'tokens' => InterfaceRole::SENDER,
        };
    }
}