<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Provider;

interface EmitterProviderInterface
{
    public function getListenersFor(string $messageName): array;
}
