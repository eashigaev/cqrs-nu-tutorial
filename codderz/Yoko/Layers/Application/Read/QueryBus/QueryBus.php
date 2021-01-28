<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageResolverInterface;

class QueryBus implements QueryBusInterface
{
    protected QueryMapperInterface $mapper;
    protected MessageResolverInterface $resolver;

    public function __construct(QueryMapperInterface $mapper, MessageResolverInterface $resolver)
    {
        $this->mapper = $mapper;
        $this->resolver = $resolver;
    }

    public function handle($message)
    {
        $handler = $this->mapper->map($message);
        $handler = $this->resolver->resolve($message, $handler);

        return $handler($message);
    }
}
