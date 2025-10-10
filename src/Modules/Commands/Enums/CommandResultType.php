<?php

namespace Ocpi\Modules\Commands\Enums;

use Ocpi\Support\Traits\Enums\FromName;

enum CommandResultType: string
{
    use FromName;

    case ACCEPTED = 'ACCEPTED';
    case CANCELED_RESERVATION = 'CANCELED_RESERVATION';
    case EVSE_OCCUPIED = 'EVSE_OCCUPIED';
    case EVSE_INOPERATIVE = 'EVSE_INOPERATIVE';
    case FAILED = 'FAILED';
    case NOT_SUPPORTED = 'NOT_SUPPORTED';
    case REJECTED = 'REJECTED';
    case TIMEOUT = 'TIMEOUT';
    case UNKNOWN_RESERVATION = 'UNKNOWN_RESERVATION';
}
