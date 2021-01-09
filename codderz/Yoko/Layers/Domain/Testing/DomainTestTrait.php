<?php

namespace Codderz\Yoko\Layers\Domain\Testing;

use Codderz\Yoko\Layers\Domain\Aggregate\AggregateInterface;

trait DomainTestTrait
{
    public function assertReleasedEvents(AggregateInterface $aggregate, array $events)
    {
        $this->assertEquals($events, $aggregate->releaseEvents());
    }
}
