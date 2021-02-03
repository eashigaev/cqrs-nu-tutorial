<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Messaging\Events\EventHandlerInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\Messages\MessageHandlerInterface;

interface ReadModelHandlerInterface extends EventHandlerInterface, MessageHandlerInterface
{
}
