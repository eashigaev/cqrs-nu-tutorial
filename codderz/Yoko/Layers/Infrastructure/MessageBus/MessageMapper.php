<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

class MessageMapper implements MessageMapperInterface
{
    public array $handlers = [];

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
