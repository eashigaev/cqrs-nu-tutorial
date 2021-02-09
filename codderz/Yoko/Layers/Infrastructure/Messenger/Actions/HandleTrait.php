<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Actions;

use Codderz\Yoko\Layers\Infrastructure\Messenger\Messenger;

trait HandleTrait
{
    public function handle($message)
    {
        return Messenger::of($this)->handle($message);
    }
}
