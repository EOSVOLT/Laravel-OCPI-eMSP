<?php

namespace Ocpi\Models\Commands\Enums;

enum CommandResultType
{
    case ACCEPTED;
    case CANCELED_RESERVATION;
    case EVSE_OCCUPIED;
    case EVSE_INOPERATIVE;
    case FAILED;
    case NOT_SUPPORTED;
    case REJECTED;
    case TIMEOUT;
    case UNKNOWN_RESERVATION;
}
