<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Events\EventHandlerTrait;
use Codderz\Yoko\Layers\Infrastructure\Dispatcher\Messages\MessageHandlerTrait;

abstract class ReadModel implements ReadModelInterface
{
    use MessageHandlerTrait;
    use EventHandlerTrait;
}
