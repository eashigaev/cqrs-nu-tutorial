<?php

namespace Codderz\Yoko\Layers\Application\Write;

use Codderz\Yoko\Layers\Application\ActionHandlerInterface;
use Codderz\Yoko\Layers\Application\Messenger;

class CommandHandler implements ActionHandlerInterface
{
    public function handle($message)
    {
        return Messenger::of($this)->handle($message);
    }
}
