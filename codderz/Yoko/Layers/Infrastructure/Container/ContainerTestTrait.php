<?php

namespace Codderz\Yoko\Layers\Infrastructure\Container;

trait ContainerTestTrait
{
    public function container(): ContainerInterface
    {
        return $this->app->make(ContainerInterface::class);
    }
}
