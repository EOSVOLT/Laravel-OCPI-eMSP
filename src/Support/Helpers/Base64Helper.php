<?php

namespace Ocpi\Support\Helpers;

use Ocpi\Models\Party;

class Base64Helper
{
    public static function isBase64Encoded(string $input): bool
    {
        if (false === preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $input)) {
            return false;
        }
        $decoded = base64_decode($input, true);
        if ($decoded === false) {
            return false;
        }
        return base64_encode($decoded) === $input;
    }
}