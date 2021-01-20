<?php

namespace Codderz\Yoko\Layers\Infrastructure\Container;

trait ContainerTestTrait
{
    public function container(): ContainerInterface
    {
        return $this->app->make(ContainerInterface::class);
    }

    public function freshInstance(string $contract)
    {
        $this->app->forgetInstance($contract);

        return $this->app->make($contract);
    }
}
