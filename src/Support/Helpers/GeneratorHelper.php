<?php

namespace Ocpi\Support\Helpers;

use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Modules\Credentials\Object\PartyCode;

class GeneratorHelper
{
    /**
     * @param string $countryCode
     * @return PartyCode
     */
    public static function generateUniquePartyCode(string $countryCode): PartyCode
    {
        do {
            $rand = strtoupper(Str::random(3));
            $partyCode = $countryCode . '*' . $rand;
        } while (true === Party::query()->where('code', $partyCode)->exists());

        return new PartyCode($partyCode, $countryCode);
    }

    /**
     * @param string $token
     * @param string $version
     * @return false|string
     * @todo move to helper or static factory
     */
    public static function decodeToken(string $token, string $version = '2.2.1'): false|string
    {
        if (version_compare($version, '2.2', '<')) {
            return $token;
        }

        if (true === Base64Helper::isBase64Encoded($token)) {
            return base64_decode($token, true);
        }
        return $token;
    }
}