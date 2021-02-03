<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Messaging\ApplyEventsTrait;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandleMessageTrait;

trait ReadModelTrait
{
    use HandleMessageTrait;
    use ApplyEventsTrait;
}
