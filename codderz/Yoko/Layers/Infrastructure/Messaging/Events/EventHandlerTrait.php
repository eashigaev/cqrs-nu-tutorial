<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\Events;

use Codderz\Yoko\Support\Reflect;

trait EventHandlerTrait
{
    public function apply($event)
    {
        $method = __FUNCTION__ . Reflect::shortClass($event);

        if (method_exists($this, $method) && Reflect::paramTypes($this, $method) === [get_class($event)]) {
            $this->$method($event);
        };

        return $this;
    }

    public function applyAll(array $events)
    {
        foreach ($events as $event) {
            $this->apply($event);
        }
        return $this;
    }
}
