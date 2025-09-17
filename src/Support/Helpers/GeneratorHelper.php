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
            $partyCode = strtoupper(Str::random(3));
            $code = $countryCode . '*' . $partyCode;
        } while (true === Party::query()->where('code', $code)->exists());

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

    public static function encodeToken(string $token, string $version = '2.2.1'): string
    {
        if (version_compare($version, '2.2', '<')) {
            return $token;
        }

        return base64_encode($token);
    }

    public static function generateToken(string $baseString): string
    {
        return $baseString . '_' . Str::uuid();
    }
}