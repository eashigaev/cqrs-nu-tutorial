<?php

namespace Codderz\Yoko\Layers\Application\Read\QueryBus;

use Codderz\Yoko\Layers\Infrastructure\MessageBus\FakeMessageBus;

trait QueryBusTestTrait
{
    public function setUpFakeQueryBus(array $handlers)
    {
        $bus = FakeMessageBus::of($handlers);

        $this->app->instance(QueryBusInterface::class, $bus);

        return $bus;
    }

    public function assertHandledQueries(FakeMessageBus $bus, array $messages)
    {
        $this->assertEquals($messages, $bus->releaseHandledMessages());
    }
}
