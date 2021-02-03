<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\MessageBus;

interface MessageBusInterface
{
    public function handle($message);
}
