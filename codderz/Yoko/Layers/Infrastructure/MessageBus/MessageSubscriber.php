<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Support\Reflect;

class MessageSubscriber implements MessageSubscriberInterface
{
    protected ContainerInterface $container;

    public array $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(string $message, $handler)
    {
        $this->handlers[$message] = $handler;
        return $this;
    }

    public function match($message)
    {
        $handler = $this->handlers[get_class($message)] ?? null;

        if (!$handler) throw new \Error(
            get_class($this) . " does not have handler for " . get_class($message)
        );

        return $this
            ->container
            ->make($handler);
    }

    public function handle($message)
    {
        $handler = $this->match($message);

        $method = lcfirst(Reflect::shortClass($message));

        if (!method_exists($handler, $method)) throw new \Error(
            get_class($this) . " does not have method for " . get_class($message)
        );

        return $handler->$method($message);
    }


}
