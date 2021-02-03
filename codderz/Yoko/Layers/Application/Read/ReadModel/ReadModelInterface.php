<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Messaging\ApplyEventsInterface;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandleMessageInterface;

interface ReadModelInterface extends ApplyEventsInterface, HandleMessageInterface
{
}
