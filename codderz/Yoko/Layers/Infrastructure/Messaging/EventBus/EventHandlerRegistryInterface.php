<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\EventBus;

interface EventHandlerRegistryInterface
{
    public function register(string $handler): self;

    public function registerAll(array $handlers): self;
}
