<?php

namespace Codderz\Yoko\Layers\Infrastructure\EventBus;

interface EventBusInterface
{
    public function publish($event);

    public function publishAll(array $events);
}
