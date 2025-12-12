<?php

namespace Ocpi\Modules\Tokens\Enums;

enum AllowedType: string
{
    case ALLOWED = 'ALLOWED';
    case BLOCKED = 'BLOCKED';
    case EXPIRED = 'EXPIRED';
    case NO_CREDIT = 'NO_CREDIT';
    case NOT_ALLOWED = 'NOT_ALLOWED';
}
