<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

trait CommandBusTestTrait
{
    public function mockCommandBus($expects = null)
    {
        return $this
            ->setUpMock(CommandBusInterface::class)
            ->expects($expects ?: $this->once())
            ->method('handle');
    }
}
