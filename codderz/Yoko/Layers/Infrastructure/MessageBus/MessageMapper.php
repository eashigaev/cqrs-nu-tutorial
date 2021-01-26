<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

class MessageMapper implements MessageMapperInterface
{
    protected ContainerInterface $container;

    public array $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function on(string $message, $handler)
    {
        $this->handlers[$message] = $handler;
        return $this;
    }

    public function map($message)
    {
        $handler = $this->handlers[get_class($message)] ?? null;

        if (!$handler) throw new \Error(
            get_class($this) . " does not have handler for " . get_class($message)
        );

        return $handler;
    }
}
