<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\Events\EventBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandlerRegistryInterface;

class EventBus implements EventBusInterface, HandlerRegistryInterface
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

    public function applyAll(array $events)
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
    }

    public function apply($event)
    {
        foreach ($this->handlers as $handler) {
            $this
                ->container
                ->make($handler)
                ->apply($event);
        }
    }
}
