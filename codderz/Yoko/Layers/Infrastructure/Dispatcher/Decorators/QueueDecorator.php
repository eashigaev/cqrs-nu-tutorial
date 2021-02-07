<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Decorators;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;

class QueueDecorator implements DispatcherInterface
{
    protected DispatcherInterface $dispatcher;

    protected array $queue = [];
    protected bool $isHandling = false;

    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch($message)
    {
        $this->queue[] = $message;

        if (!$this->isHandling) {
            $this->isHandling = true;

            while ($message = array_shift($this->queue)) {
                $this->dispatcher->dispatch($message);
            }

            $this->isHandling = false;
        }
    }
}
