<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\MessageBus;

class QueryBus extends MessageBus implements QueryBusInterface, QueryResolverInterface
{
}
