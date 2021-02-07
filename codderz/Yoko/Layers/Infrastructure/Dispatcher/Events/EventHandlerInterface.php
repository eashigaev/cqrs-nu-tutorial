<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Events;

interface EventHandlerInterface
{
    public function apply($event);

    public static function getHandledEvents(): array;
}
