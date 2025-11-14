<?php

namespace Ocpi\Support\Client;

use Ocpi\Support\Enums\InterfaceRole;

class SenderClient extends Client
{
    protected InterfaceRole $interfaceRole = InterfaceRole::SENDER;
}
