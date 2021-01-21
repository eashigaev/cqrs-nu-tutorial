<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

interface QueryBusInterface
{
    public function subscribe(string $query, string $handler);

    public function handle($query);

    public function releaseQueries();
}
