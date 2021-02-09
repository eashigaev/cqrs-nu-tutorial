<?php

namespace Codderz\Yoko\Layers\Application\Events\EventEmitter;

interface EventHandlerInterface
{
    public function apply($event);

    public function applyAll(array $events);
}
