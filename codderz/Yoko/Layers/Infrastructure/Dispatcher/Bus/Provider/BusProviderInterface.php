<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\Provider;

interface BusProviderInterface
{
    public function getHandlerFor(string $messageName);
}
