<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging;

interface HandlerRegistryInterface
{
    public function register(string $handler): self;

    public function registerAll(array $handlers): self;
}
