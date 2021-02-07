<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus;

interface BusInterface
{
    public function handle($message);
}
