<?php

namespace Tests\Traits;

trait SchemaTrait
{
    public static function getJsonResourceArray(string $fileName): array
    {
        $schema = file_get_contents($fileName);
        return json_decode($schema, true);
    }

    public static function getJsonResourceString(string $fileName): array
    {
        $schema = file_get_contents($fileName);
        return json_decode($schema);
    }
}