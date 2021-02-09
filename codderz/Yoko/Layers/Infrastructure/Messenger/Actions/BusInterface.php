<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Actions;

interface BusInterface
{
    public function handle($message);
}
