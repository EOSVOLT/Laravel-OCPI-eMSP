<?php

namespace Tests\Traits;

trait SchemaTrait
{
    public function getJsonResourceArray(string $fileName): array
    {
        $schema = file_get_contents($fileName);
        return json_decode($schema, true);
    }
}