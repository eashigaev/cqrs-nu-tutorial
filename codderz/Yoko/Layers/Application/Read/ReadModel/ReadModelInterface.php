<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Events\EventHandlerInterface;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Messages\MessageHandlerInterface;

interface ReadModelInterface extends EventHandlerInterface, MessageHandlerInterface
{
}
