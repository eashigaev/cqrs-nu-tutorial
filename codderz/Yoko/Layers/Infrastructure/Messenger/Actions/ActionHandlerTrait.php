<?php

namespace Codderz\Yoko\Layers\Infrastructure\Messenger\Actions;

use Codderz\Yoko\Layers\Infrastructure\Dispatcher\NotDispatched;
use Codderz\Yoko\Support\Reflect;

trait ActionHandlerTrait
{
    public function handle($message)
    {
        $method = lcfirst(Reflect::shortClass($message));

        if (method_exists($this, $method) && Reflect::paramTypes($this, $method) === [get_class($message)]) {
            return $this->$method($message);
        }

        throw NotDispatched::new();
    }
}
