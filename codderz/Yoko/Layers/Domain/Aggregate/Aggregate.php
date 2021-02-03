<?php

namespace Codderz\Yoko\Layers\Domain\Aggregate;

use Codderz\Yoko\Layers\Infrastructure\Messaging\Events\EventHandlerTrait;

class Aggregate implements AggregateInterface
{
    use EventHandlerTrait;

    protected array $recordedEvents = [];

    public function recordThat($event)
    {
        $this->recordedEvents[] = $event;

        $this->apply($event);

        return $this;
    }

    public function releaseEvents()
    {
        $releasedEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $releasedEvents;
    }

    public static function fromEvents(array $events = [])
    {
        $self = new static;
        $self->applyAll($events);
        return $self;
    }
}
