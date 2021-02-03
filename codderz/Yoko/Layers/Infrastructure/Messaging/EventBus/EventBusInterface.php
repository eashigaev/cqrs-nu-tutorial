<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\EventBus;

interface EventBusInterface
{
    public function publish($event);

    public function publishAll(array $events);
}
