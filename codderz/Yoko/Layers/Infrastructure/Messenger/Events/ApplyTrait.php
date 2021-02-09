<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Events;

use Codderz\Yoko\Layers\Infrastructure\Messenger\Messenger;

trait ApplyTrait
{
    public function apply($event)
    {
        return Messenger::of($this)->apply($event);
    }

    public function applyAll(array $events)
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
        return $this;
    }
}
