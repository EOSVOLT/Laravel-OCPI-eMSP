<?php

namespace Ocpi\Modules\Credentials\Object;

use Eosvolt\Foundation\TypedCollection;

class PartyTokenCollection extends TypedCollection
{
    protected string $type = PartyToken::class;
}