<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Messages;

interface MessageHandlerInterface
{
    public function handle($message);

    public static function getHandledMessages(): array;
}
