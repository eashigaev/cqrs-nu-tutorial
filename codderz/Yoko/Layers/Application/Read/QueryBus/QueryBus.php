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

    public function subscribe(string $query, $handler)
    {
        $this->handlers[$query] = $handler;
        return $this;
    }

    public function handle($query)
    {
        $handler = $this->handlers[get_class($query)] ?? null;

        if (!$handler) throw CommonException::new('Bus can not handle query ' . get_class($query));

        return is_callable($handler)
            ? $handler($query)
            : $this
                ->container
                ->make($handler)
                ->handle($query);
    }
}
