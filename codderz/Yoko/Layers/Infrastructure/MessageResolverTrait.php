<?php

namespace Codderz\Yoko\Layers\Infrastructure;

use Codderz\Yoko\Support\Reflect;

trait MessageResolverTrait
{
    public function handle($message)
    {
        $method = lcfirst(Reflect::shortClass($message));

        if (method_exists($this, $method)) {
            return $this->$method($message);
        };
    }
}
