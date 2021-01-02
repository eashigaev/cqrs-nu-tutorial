<?php

namespace Codderz\Yoko\Domain;

use Codderz\Yoko\Support\Reflect;

class Aggregate
{
    protected $recordedEvents = [];

    public function handle($command)
    {
        $method = __FUNCTION__ . Reflect::shortClass($command);

        if (method_exists($this, $method)) {
            $this->$method($command);
            return $this;
        }

        throw new \Error(
            get_class($this) . " does not yet handle command " . get_class($command)
        );
    }

    public function recordThat($event)
    {
        $this->recordedEvents[] = $event;

        $this->apply($event);

        return $this;
    }

    public function apply($event)
    {
        $method = __FUNCTION__ . Reflect::shortClass($event);

        if (method_exists($this, $method)) {
            $this->$method($event);
            return $this;
        };

        throw new \Error(
            get_class($this) . " does not know how to apply event " . get_class($event)
        );
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
        foreach ($events as $event) {
            $self->apply($event);
        }
        return $self;
    }
}
