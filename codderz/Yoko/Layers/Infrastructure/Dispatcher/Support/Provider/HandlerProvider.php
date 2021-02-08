<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Provider;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

class HandlerProvider implements HandlerProviderInterface
{
    protected ContainerInterface $container;
    protected array $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function subscribe(array $handlers): self
    {
        foreach ($handlers as $messageName => $handler) {
            if (is_array($handler)) {
                $this->listenMany($messageName, $handler);
                continue;
            }
            $this->listen($messageName, $handler);
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
