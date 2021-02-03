<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messaging\Messages;

interface MessageHandlerInterface
{
    public function handle($message);
}
