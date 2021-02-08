<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Resolver;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

class HandlerResolver implements HandlerResolverInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve($handler, $method)
    {
        if (is_callable($handler)) return $handler;

        if (is_string($handler)) $handler = $this->container->make($handler);

        return fn($message) => $handler->$method($message);
    }
}
