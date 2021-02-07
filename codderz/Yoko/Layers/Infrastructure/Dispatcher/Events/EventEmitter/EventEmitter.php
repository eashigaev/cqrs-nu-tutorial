<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Events\EventEmitter;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\HandlerRegistryInterface;

class EventEmitter implements EventEmitterInterface, HandlerRegistryInterface
{
    protected ContainerInterface $container;
    protected $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function register(string $handler): self
    {
        if (!array_key_exists($handler, $this->handlers)) {
            $this->handlers[] = $handler;
        }
        return $this;
    }

    public function registerAll(array $handlers): self
    {
        foreach ($handlers as $handler) {
            $this->register($handler);
        }
        return $this;
    }

    public function emitAll(array $events)
    {
        foreach ($events as $event) {
            $this->emit($event);
        }
    }

    public function emit($event)
    {
        foreach ($this->handlers as $handler) {
            $this
                ->container
                ->make($handler)
                ->apply($event);
        }
    }
}
