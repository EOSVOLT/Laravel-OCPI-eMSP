<?php

namespace Ocpi\Models\Commands\Enums;

enum CommandType
{
    case CANCEL_RESERVATION;
    case RESERVE_NOW;
    case START_SESSION;
    case STOP_SESSION;
    case UNLOCK_CONNECTOR;
}
