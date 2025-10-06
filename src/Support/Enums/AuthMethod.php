<?php

namespace Ocpi\Support\Enums;

enum AuthMethod: string
{
    case AUTH_REQUEST = 'AUTH_REQUEST';
    case COMMAND = 'COMMAND';
    case WHITELIST = 'WHITELIST';
}
