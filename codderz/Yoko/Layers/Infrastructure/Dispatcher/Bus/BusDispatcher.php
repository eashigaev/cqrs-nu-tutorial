<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\NotDispatched;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Provider\HandlerProviderInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Resolver\HandlerResolverInterface;

class BusDispatcher implements DispatcherInterface
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
            $handler = $this->resolver->resolve($handler, 'handle');
            return $handler($message);
        }

        throw NotDispatched::new(
            get_class($this) . " does not have handler for " . get_class($message)
        );
    }
}
