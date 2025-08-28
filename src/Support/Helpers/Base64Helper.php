<?php

namespace Ocpi\Support\Helpers;

class Base64Helper
{
    public static function isBase64Encoded(string $input): bool
    {
        $decoded = base64_decode($input, true);
        if ($decoded === false) {
            return false;
        }
        return base64_encode($decoded) === $input;
    }
}