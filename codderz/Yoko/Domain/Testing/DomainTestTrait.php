<?php

namespace Codderz\Yoko\Domain\Testing;

use Codderz\Yoko\Domain\Aggregate;

trait DomainTestTrait
{
    public function assertReleasedEvents(Aggregate $aggregate, array $events)
    {
        $this->assertEquals($events, $aggregate->releaseEvents());
    }
}
