<?php

namespace Tests\Unit\Presentation\Api;

use Codderz\Yoko\Layers\Application\Read\QueryBus\QueryBusInterface;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerTestTrait;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithoutMiddleware,
        ContainerTestTrait;

    protected QueryBusInterface $queryBus;

    public function setUp(): void
    {
        parent::setUp();
        $this->queryBus = $this->freshInstance(QueryBusInterface::class);
    }
}
