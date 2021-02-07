<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter;

interface EmitterInterface
{
    public function emit($message);

    public function emitAll(array $messages);
}
