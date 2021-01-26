<?php

namespace Codderz\Yoko\Layers\Infrastructure\Container;

trait ContainerTestTrait
{
    public function container(): ContainerInterface
    {
        return $this->app->make(ContainerInterface::class);
    }

    public function setUpMock(string $contract)
    {
        $mock = $this->createMock($contract);

        $this->app->instance($contract, $mock);

        return $mock;
    }
}
