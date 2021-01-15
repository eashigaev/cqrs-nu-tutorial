<?php

namespace Codderz\Yoko\Layers\Infrastructure\Container;

use Illuminate\Container\Container as BaseContainer;

class Container implements ContainerInterface
{
    protected BaseContainer $container;

    public function __construct(BaseContainer $container)
    {
        $this->container = $container;
    }

    public function make(string $contract)
    {
        return $this->container->make($contract);
    }
}
