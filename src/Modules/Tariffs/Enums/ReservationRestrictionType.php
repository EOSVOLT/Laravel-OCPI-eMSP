<?php

namespace Ocpi\Modules\Tariffs\Enums;

enum ReservationRestrictionType: string
{
    case RESERVATION = 'RESERVATION';
    case RESERVATION_EXPIRES = 'RESERVATION_EXPIRES';
}