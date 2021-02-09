<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Application\Messenger;

class ReadModel implements ReadModelInterface
{
    public function handle($message)
    {
        return Messenger::of($this)->handle($message);
    }

    public function apply($event)
    {
        Messenger::of($this)->apply($event);
        return $this;
    }

    public function applyAll(array $events)
    {
        foreach ($events as $event) {
            Messenger::of($this)->apply($event);
        }
        return $this;
    }
}
