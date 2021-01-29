<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Support\Reflect;

class MessageBus implements MessageBusInterface, MessageResolverInterface
{
    protected ContainerInterface $container;
    protected $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function on(string $message, $handler)
    {
        $this->handlers[$message] = $handler;
        return $this;
    }

    public function resolve($message)
    {
        $handler = $this->handlers[get_class($message)] ?? null;

        if (!$handler) throw new \Error(
            get_class($this) . " does not have handler for " . get_class($message)
        );

        return $handler;
    }

    public function handle($message)
    {
        $handler = $this->resolve($message);

        if (is_callable($handler)) return $handler($message);

        if (!is_object($handler)) {
            $handler = $this->container->make($handler);
        }

        $method = lcfirst(Reflect::shortClass($message));

        if (!method_exists($handler, $method)) throw new \Error(
            get_class($this) . " does not have method for " . get_class($message)
        );

        return $handler->$method($message);
    }
}
