<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Application\Read\QueryHandlerInterface;

interface QueryBusInterface extends QueryHandlerInterface
{
    public function subscribe(string $query, string $handler);
}
