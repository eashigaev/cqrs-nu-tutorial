<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

use Codderz\Yoko\CommonException;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

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

    public function handle($message)
    {
        $handler = $this->handlers[get_class($message)] ?? null;

        if (!$handler) throw new CommonException('Bus can not handle message ' . get_class($message));

        if (is_callable($handler)) return $handler($message);

        if (is_object($handler)) return $handler->handle($message);

        return $this
            ->container
            ->make($handler)
            ->handle($message);
    }
}
