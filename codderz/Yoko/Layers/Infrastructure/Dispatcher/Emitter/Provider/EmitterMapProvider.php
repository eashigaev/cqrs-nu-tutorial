<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Provider;

class EmitterMapProvider implements EmitterProviderInterface
{
    protected array $handlers = [];

    public function map(array $handlersMap): self
    {
        foreach ($handlersMap as $messageName => $handlers) {
            $this->listenMany($messageName, $handlers);
        }
        return $this;
    }

    public function listenMany(string $messageName, array $handlers): self
    {
        foreach ($handlers as $handler) {
            $this->listen($messageName, $handler);
        }
        return $this;
    }

    public function listen(string $messageName, $handler): self
    {
        $this->handlers[$messageName][] = $handler;
        return $this;
    }

    public function getListenersFor(string $messageName): array
    {
        return $this->handlers[$messageName] ?? [];
    }
}
