<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\Provider;

class BusMapProvider implements BusProviderInterface
{
    protected array $handlers = [];

    public function map(array $handlersMap)
    {
        foreach ($handlersMap as $messageName => $handler) {
            $this->register($messageName, $handler);
        }
        return $this;
    }

    public function register(string $messageName, $handler)
    {
        $this->handlers[$messageName] = $handler;
        return $this;
    }

    public function getHandlerFor(string $messageName)
    {
        return $this->handlers[$messageName] ?? null;
    }
}
