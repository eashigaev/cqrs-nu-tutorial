<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\MessageBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandleMessageException;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandlerRegistryInterface;

class MessageBus implements MessageBusInterface, HandlerRegistryInterface
{
    protected ContainerInterface $container;
    protected $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function register(string $handler): self
    {
        if (!array_key_exists($handler, $this->handlers)) {
            $this->handlers[] = $handler;
        }
        return $this;
    }

    public function registerAll(array $handlers): self
    {
        foreach ($handlers as $handler) {
            $this->register($handler);
        }
        return $this;
    }

    public function handle($message)
    {
        foreach ($this->handlers as $handler) {
            try {
                return $this
                    ->container
                    ->make($handler)
                    ->handle($message);
            } catch (HandleMessageException $exception) {
                continue;
            }
        }

        throw new \Error(
            get_class($this) . " does not have handler for " . get_class($message)
        );
    }
}
