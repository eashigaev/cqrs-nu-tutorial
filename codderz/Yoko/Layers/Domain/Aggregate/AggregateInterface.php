<?php

namespace Codderz\Yoko\Layers\Domain\Aggregate;

interface AggregateInterface
{
    public function handle($command);

    public function apply($event);

    public function recordThat($event);

    public function releaseEvents();
}
