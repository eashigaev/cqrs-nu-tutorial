<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Messaging\Events\EventHandlerTrait;
use Codderz\Yoko\Layers\Infrastructure\Messaging\Messages\MessageHandlerTrait;

trait ReadModelTrait
{
    use MessageHandlerTrait;
    use EventHandlerTrait;
}
