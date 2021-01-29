<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

interface MessageResolverInterface
{
    public function on(string $message, $handler);

    public function resolve($message);
}
