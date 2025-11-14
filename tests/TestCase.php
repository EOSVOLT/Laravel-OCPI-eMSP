<?php

namespace Ocpi\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        gc_enable();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
        gc_collect_cycles();
    }
}
