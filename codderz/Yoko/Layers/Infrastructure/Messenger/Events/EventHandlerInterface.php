<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Events;

interface EventHandlerInterface
{
    public function apply($event);
}
