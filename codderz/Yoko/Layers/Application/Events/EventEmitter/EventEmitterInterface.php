<?php

namespace Codderz\Yoko\Layers\Application\Events\EventEmitter;

interface EventEmitterInterface
{
    public function emit($message);

    public function emitAll(array $messages);
}
