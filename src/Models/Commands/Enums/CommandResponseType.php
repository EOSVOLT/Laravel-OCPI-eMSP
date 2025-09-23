<?php

namespace Ocpi\Models\Commands\Enums;

use Ocpi\Support\Traits\Enums\FromName;

enum CommandResponseType: string
{
    use FromName;

    case NOT_SUPPORTED = 'NOT_SUPPORTED';
    case REJECTED = 'REJECTED';
    case ACCEPTED = 'ACCEPTED';
    case TIMEOUT = 'TIMEOUT';
    case UNKNOWN_SESSION = 'UNKNOWN_SESSION';
}
