<?php

namespace Codderz\Yoko\Layers\Infrastructure\EventBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Support\Reflect;

class EventBus implements EventBusInterface, EventResolverInterface
{
    const METHOD_PREFIX = 'apply';

    protected ContainerInterface $container;
    protected $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function bind($handler, string $event): self
    {
        if (!array_key_exists($event, $this->handlers)) {
            $this->handlers[$event] = [];
        }
        $this->handlers[$event][] = $handler;
        return $this;
    }

    public function bindAll($handler, array $events): self
    {
        foreach ($events as $event) {
            $this->bind($handler, $event);
        }
        return $this;
    }

    public function resolve($event)
    {
        return $this->handlers[get_class($event)] ?? [];
    }

    public function publishAll(array $events)
    {
        foreach ($events as $event) {
            $this->publish($event);
        }
    }

    public function publish($event)
    {
        $handlers = $this->resolve($event);

        foreach ($handlers as $handler) {
            $this->handle($event, $handler);
        }
    }

    protected function handle($event, $handler)
    {
        if (is_callable($handler)) return $handler($event);

        if (!is_object($handler)) {
            $handler = $this->container->make($handler);
        }

        $method = static::METHOD_PREFIX;

        if (!method_exists($handler, $method)) {
            $method .= ucfirst(Reflect::shortClass($event));
        }

        if (!method_exists($handler, $method)) throw new \Error(
            get_class($handler) . " does not have method for " . get_class($event)
        );

        return $handler->$method($event);
    }
}
