<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging;

interface ApplyEventsInterface
{
    public function apply($event);

    public function applyAll(array $events);
}
