<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\BusDispatcher;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;

class QueryBus implements QueryBusInterface
{
    protected DispatcherInterface $dispatcher;

    public function __construct(BusDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle($message)
    {
        return $this->dispatcher->dispatch($message);
    }
}
