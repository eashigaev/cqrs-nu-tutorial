<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\Events;

interface EventHandlerInterface
{
    public function apply($event);

    public function applyAll(array $events);
}
