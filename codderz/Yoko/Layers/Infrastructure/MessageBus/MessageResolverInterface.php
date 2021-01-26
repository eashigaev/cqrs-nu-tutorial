<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

interface MessageResolverInterface
{
    public function resolve($message, $handler);
}
