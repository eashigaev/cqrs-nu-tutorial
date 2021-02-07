<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Actions;

interface ActionHandlerInterface
{
    public function handle($message);
}
