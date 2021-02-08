<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Provider;

interface HandlerProviderInterface
{
    public function subscribe(array $handlers): self;

    public function listenMany(string $messageName, array $handlers): self;

    public function listen(string $messageName, $handler): self;

    public function getListenersFor(string $messageName): array;
}
