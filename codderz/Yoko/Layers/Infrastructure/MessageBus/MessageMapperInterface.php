<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

interface MessageMapperInterface
{
    public function on(string $message, $handler);

    public function map($message);
}
