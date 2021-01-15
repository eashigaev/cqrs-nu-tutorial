<?php

namespace Codderz\Yoko\Layers\Domain;

trait DomainTestTrait
{
    public function assertReleasedEvents(AggregateInterface $aggregate, array $events)
    {
        $this->assertEquals($events, $aggregate->releaseEvents());
    }
}
