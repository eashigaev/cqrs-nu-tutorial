<?php

namespace Codderz\Yoko\Layers\Infrastructure\EventBus;

interface EventResolverInterface
{
    public function bind($handler, string $message): self;

    public function bindAll($handler, array $messages): self;

    public function resolve($message);
}
