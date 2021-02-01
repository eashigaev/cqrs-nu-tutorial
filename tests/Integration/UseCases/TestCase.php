<?php

namespace Tests\Integration\UseCases;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusTestTrait;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusTestTrait;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FixtureTestTrait;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations,
        FixtureTestTrait,
        ContainerTestTrait,
        CommandBusTestTrait,
        QueryBusTestTrait;
}
