<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\CommonException;
use Codderz\Yoko\Layers\Infrastructure\Container\ContainerInterface;

class QueryBus implements QueryBusInterface
{
    protected ContainerInterface $container;

    public array $handlers = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function subscribe(string $message, string $handler)
    {
        $this->handlers[$message] = $handler;
        return $this;
    }

    public function handle($message)
    {
        $handler = $this->handlers[get_class($message)] ?? null;

        if (!$handler) throw CommonException::new('Handler not found');

        return $this
            ->container
            ->make($handler)
            ->handle($message);
    }
}
