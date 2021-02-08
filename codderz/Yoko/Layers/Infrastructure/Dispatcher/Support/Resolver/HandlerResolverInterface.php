<?php

namespace Codderz\Yoko\Layers\Infrastructure\Dispatcher\Support\Resolver;

interface HandlerResolverInterface
{
    public function resolve($handler, $method);
}
