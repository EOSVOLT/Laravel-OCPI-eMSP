<?php

namespace Ocpi\Support\Helpers;

use Illuminate\Support\Str;
use Ocpi\Models\Party;

class GeneratorHelper
{
    /**
     * @return string
     */
    public static function generateUniquePartyId(): string
    {
        do {
            $randomString = strtoupper(Str::random(3));
        } while (true === Party::query()->where('code', $randomString)->exists());

        return $randomString;
    }
}