<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter\Provider\EmitterProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Factory\HandlerFactoryInterface;

class EmitterDispatcher implements DispatcherInterface
{
    protected EmitterProviderInterface $provider;
    protected HandlerFactoryInterface $factory;

    public function __construct(EmitterProviderInterface $provider, HandlerFactoryInterface $factory)
    {
        $this->provider = $provider;
        $this->factory = $factory;
    }

    public function dispatch($message)
    {
        foreach ($this->provider->getListenersFor(get_class($message)) as $handler) {
            $callable = $this->factory->make($handler, 'apply');
            $callable($message);
        }
    }
}
