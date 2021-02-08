<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Decorators;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;

class QueueDecorator implements DispatcherInterface
{
    protected DispatcherInterface $dispatcher;

    protected array $queue = [];
    protected bool $isHandling = false;

    public static function of(DispatcherInterface $dispatcher)
    {
        $self = new self();
        $self->dispatcher = $dispatcher;
        return $self;
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
