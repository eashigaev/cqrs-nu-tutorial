<?php

namespace Codderz\Yoko\Layers\Infrastructure\EventBus;

interface EventBusInterface
{
    public function subscribe(string $eventType, string $handler);

    public function publishAll(array $events);
}
