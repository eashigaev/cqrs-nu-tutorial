<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandleMessageException;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandleMessageInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandlerRegistryInterface;

class QueryBus implements QueryBusInterface, HandlerRegistryInterface
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

            if (!is_subclass_of($handler, HandleMessageInterface::class)) {
                continue;
            }

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
