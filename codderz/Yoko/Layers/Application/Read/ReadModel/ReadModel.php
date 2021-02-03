<?php

namespace Codderz\Yoko\Layers\Application\Read\ReadModel;

use Codderz\Yoko\Layers\Infrastructure\Messaging\ApplyEventsTrait;
use Codderz\Yoko\Layers\Infrastructure\Messaging\HandleMessageTrait;

class ReadModel implements ReadModelInterface
{
    use HandleMessageTrait;
    use ApplyEventsTrait;
}
