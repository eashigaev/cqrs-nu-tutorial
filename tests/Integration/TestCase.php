<?php

namespace Tests\Integration;

use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusTestTrait;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FixtureTestTrait;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations,
        FixtureTestTrait,
        CommandBusTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
    }
}
