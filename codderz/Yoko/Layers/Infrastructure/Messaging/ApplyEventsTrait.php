<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging;

use Codderz\Yoko\Support\Reflect;

trait ApplyEventsTrait
{
    public function apply($event)
    {
        $method = __FUNCTION__ . Reflect::shortClass($event);

        if (method_exists($this, $method)) {
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
