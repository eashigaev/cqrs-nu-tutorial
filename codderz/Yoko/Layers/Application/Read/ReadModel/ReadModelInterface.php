<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

interface ReadModelInterface
{
    public function apply($event);

    public function withEvents(array $events = []);
}
