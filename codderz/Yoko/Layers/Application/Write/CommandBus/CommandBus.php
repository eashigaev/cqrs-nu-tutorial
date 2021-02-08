<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Bus\BusDispatcher;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\DispatcherInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Decorators\QueueDecorator;

class CommandBus implements CommandBusInterface
{
    protected DispatcherInterface $dispatcher;

    public function __construct(BusDispatcher $dispatcher)
    {
        $this->dispatcher = QueueDecorator::of($dispatcher);
    }

    public function handle($message)
    {
        $this->dispatcher->dispatch($message);
    }
}
