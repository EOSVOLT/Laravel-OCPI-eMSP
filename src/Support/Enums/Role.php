<?php

namespace Ocpi\Support\Enums;

enum Role: string
{
    case CPO = "cpo";
    case EMSP = "emsp";
    case HUB = "hub";
    case NAP = "nap";
    case NSP = "nsp";
    case OTHER = "other";
    case SCSP = "scsp";
}
