<?php

namespace Codderz\Yoko\Layers\Application\Write\CommandBus;

use Codderz\Yoko\Support\Collection;

trait CommandBusTestTrait
{
    public function handleCommands(array $commands)
    {
        Collection::of($commands)
            ->each(fn($command) => $this->commandBus()->handle($command));
    }

    public function commandBus(): CommandBusInterface
    {
        return $this->container()->make(CommandBusInterface::class);
    }

    public function mockCommandBus($expects = null)
    {
        return $this
            ->setUpMock(CommandBusInterface::class)
            ->expects($expects ?: $this->once())
            ->method('handle');
    }
}
