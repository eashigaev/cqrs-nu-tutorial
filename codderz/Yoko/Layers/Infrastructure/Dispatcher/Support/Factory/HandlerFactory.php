<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Factory;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

class HandlerFactory implements HandlerFactoryInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function make($handler, $method)
    {
        if (is_array($handler)) {
            list($handler, $method) = $handler;
        }

        if (is_string($handler)) {
            $handler = $this->container->make($handler);
        }

        if (method_exists($handler, $method)) {
            return [$handler, $method];
        }

        return $handler;
    }
}
