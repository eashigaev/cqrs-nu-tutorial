<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

interface QueryBusInterface
{
    public function handle($query);
}
