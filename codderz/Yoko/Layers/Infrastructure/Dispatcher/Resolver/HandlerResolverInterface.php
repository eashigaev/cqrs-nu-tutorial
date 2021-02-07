<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Resolver;

interface HandlerResolverInterface
{
    public function resolve($handler, $method);
}
