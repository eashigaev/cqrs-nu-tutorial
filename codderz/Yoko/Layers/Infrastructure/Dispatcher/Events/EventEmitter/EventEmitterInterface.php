<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Events\EventEmitter;

interface EventEmitterInterface
{
    public function emit($event);

    public function emitAll(array $events);
}
