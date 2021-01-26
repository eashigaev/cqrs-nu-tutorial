<?php

namespace Codderz\Yoko\Layers\Infrastructure\MessageBus;

interface MessageBusInterface
{
    public function handle($message);
}
