<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

trait QueryBusTestTrait
{
    public function mockQueryBus($expects = null)
    {
        return $this
            ->setUpMock(QueryBusInterface::class)
            ->expects($expects ?: $this->once())
            ->method('handle');
    }
}
