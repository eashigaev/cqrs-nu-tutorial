<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

interface CommandBusInterface
{
    public function execute($message);
}
