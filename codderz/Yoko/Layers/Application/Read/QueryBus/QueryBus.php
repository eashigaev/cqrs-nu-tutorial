<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageBus;
use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageResolverInterface;

class QueryBus extends MessageBus implements QueryBusInterface
{
    public function __construct(QueryMapperInterface $mapper, MessageResolverInterface $resolver)
    {
        parent::__construct($mapper, $resolver);
    }
}
