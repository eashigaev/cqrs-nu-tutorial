<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusTestTrait;
use Codderz\Yoko\Layers\Application\Write\CommandBus\CommandBusTestTrait;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\FixtureTestTrait;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithoutMiddleware,
        ContainerTestTrait,
        QueryBusTestTrait,
        CommandBusTestTrait,
        FixtureTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFixture();
    }
}
