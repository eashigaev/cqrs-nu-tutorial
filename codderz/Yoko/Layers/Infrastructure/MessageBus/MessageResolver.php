<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Support\Reflect;

class MessageResolver implements MessageResolverInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve($message, $handler): callable
    {
        $handler = $this->container->make($handler);

        $method = lcfirst(Reflect::shortClass($message));

        if (!method_exists($handler, $method)) throw new \Error(
            get_class($this) . " does not have method for " . get_class($message)
        );

        return fn($message) => $handler->$method($message);
    }


}
