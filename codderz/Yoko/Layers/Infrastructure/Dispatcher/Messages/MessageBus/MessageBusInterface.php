<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Messages\MessageBus;

interface MessageBusInterface
{
    public function handle($message);
}
