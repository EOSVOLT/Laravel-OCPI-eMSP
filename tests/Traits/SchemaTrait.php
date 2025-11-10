<?php

namespace Tests\Traits;

trait SchemaTrait
{
    public static function getJsonResource(string $fileName): array
    {
        $schema = file_get_contents($fileName);
        return json_decode($schema);
    }
}