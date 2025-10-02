<?php

namespace Ocpi\Support\Enums;

enum SessionStatus: string
{
    case ACTIVE = 'ACTIVE';
    case COMPLETED = 'COMPLETED';
    case INVALID = 'INVALID';
    case PENDING = 'PENDING';
    case RESERVATION = 'RESERVATION';
}
