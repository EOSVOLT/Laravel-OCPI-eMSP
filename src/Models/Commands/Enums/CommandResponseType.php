<?php

namespace Ocpi\Models\Commands\Enums;

use Ocpi\Support\Traits\Enums\FromName;

enum CommandResponseType
{
    use FromName;

    case NOT_SUPPORTED;
    case REJECTED;
    case ACCEPTED;
    case TIMEOUT;
    case UNKNOWN_SESSION;
}
