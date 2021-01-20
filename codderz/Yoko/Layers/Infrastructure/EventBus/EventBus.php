<?php

namespace Codderz\Yoko\Layers\Infrastructure\EventBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

class EventBus implements EventBusInterface
{
    protected ContainerInterface $container;

    public array $publishedEvents = [];
    public array $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function subscribe(string $message, string $handler)
    {
        if (!$this->handlers[$message]) {
            $this->handlers[$message] = [];
        }
        $this->handlers[$message][] = $handler;
        return $this;
    }

    public function publishAll(array $events)
    {
        $this->publishedEvents += $events;

        foreach ($events as $event) {
            $this->publish($event);
        }
    }

    protected function publish($event)
    {
        foreach ($this->handlers as $handler) {
            $this
                ->container
                ->make($handler)
                ->apply($event);
        }
    }

    public function publishedMessages(): array
    {
        return $this->publishedEvents;
    }
}
