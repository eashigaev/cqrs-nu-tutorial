<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\BusDispatcher;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Decorators\QueueDecorator;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;

class CommandBus implements CommandBusInterface
{
    protected DispatcherInterface $dispatcher;

    public function __construct(BusDispatcher $dispatcher)
    {
        $this->dispatcher = new QueueDecorator($dispatcher);
    }

    public function handle($message)
    {
        $this->dispatcher->dispatch($message);
    }
}
