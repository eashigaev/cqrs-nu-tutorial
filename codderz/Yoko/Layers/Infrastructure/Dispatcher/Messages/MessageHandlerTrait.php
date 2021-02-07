<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Messages;

use Codderz\Yoko\Support\Reflect;

trait MessageHandlerTrait
{
    public function handle($message)
    {
        $method = lcfirst(Reflect::shortClass($message));

        if (method_exists($this, $method) && Reflect::paramTypes($this, $method) === [get_class($message)]) {
            return $this->$method($message);
        }

        throw MessageNotHandled::new();
    }
}
