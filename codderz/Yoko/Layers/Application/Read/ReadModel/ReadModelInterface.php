<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Application\Read\QueryHandlerInterface;

interface ReadModelInterface extends QueryHandlerInterface
{
    public function apply($event);

    public function withEvents(array $events = []);

    public function mock(string $query, callable $callback);
}
