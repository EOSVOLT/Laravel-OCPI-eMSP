<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Ocpi\Helpers\TypeCollection;

class SignedValueCollection extends TypeCollection
{
    /**
     * @var string
     */
    protected string $type = SignedValue::class;
}