<?php

namespace Ocpi\Support\Client;

use Ocpi\Support\Enums\InterfaceRole;

class ReceiverClient extends Client
{
    protected InterfaceRole $interfaceRole = InterfaceRole::RECEIVER;
}
