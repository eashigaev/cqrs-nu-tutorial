<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Emitter;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Provider\HandlerProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Resolver\HandlerResolverInterface;

class EmitterDispatcher implements DispatcherInterface
{
    protected HandlerProviderInterface $provider;
    protected HandlerResolverInterface $resolver;

    public function __construct(HandlerProviderInterface $provider, HandlerResolverInterface $resolver)
    {
        $this->provider = $provider;
        $this->resolver = $resolver;
    }

    public function dispatch($message)
    {
        foreach ($this->provider->getListenersFor(get_class($message)) as $handler) {
            $handler = $this->resolver->resolve($handler, 'apply');
            $handler($message);
        }
    }
}
