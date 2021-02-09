<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Events;

interface EmitterInterface
{
    public function emit($message);

    public function emitAll(array $messages);
}
