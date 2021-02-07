<?php

namespace Codderz\Yoko\Layers\Application\Events\EventEmitter;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\EmitterDispatcher;

class EventEmitter implements EventEmitterInterface
{
    protected DispatcherInterface $dispatcher;

    public function __construct(EmitterDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function emit($message)
    {
        $this->dispatcher->dispatch($message);
    }

    public function emitAll(array $messages)
    {
        foreach ($messages as $message) {
            $this->dispatcher->dispatch($message);
        }
    }
}
