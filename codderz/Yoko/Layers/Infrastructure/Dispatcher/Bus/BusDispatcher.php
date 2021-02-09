<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\Provider\BusProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\NotDispatched;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Factory\HandlerFactoryInterface;

class BusDispatcher implements DispatcherInterface
{
    protected BusProviderInterface $provider;
    protected HandlerFactoryInterface $factory;

    public function __construct(BusProviderInterface $provider, HandlerFactoryInterface $factory)
    {
        $this->provider = $provider;
        $this->factory = $factory;
    }

    public function dispatch($message)
    {
        $handler = $this->provider->getHandlerFor(get_class($message));

        if (!$handler) throw NotDispatched::new(
            get_class($this) . " does not have handler for " . get_class($message)
        );

        $callable = $this->factory->make($handler, 'handle');

        return $callable($message);
    }
}
