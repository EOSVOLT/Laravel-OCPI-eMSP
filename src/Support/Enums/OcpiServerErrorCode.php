<?php

namespace Ocpi\Support\Enums;

enum OcpiServerErrorCode: int
{
    case Generic = 3000;
    case PartyApiUnusable = 3001;
    case UnsupportedVersion = 3002;
    case EndpointsMismatch = 3003;
}
